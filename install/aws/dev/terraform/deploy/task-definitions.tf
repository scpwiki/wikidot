module "cache" {
  source = "github.com/cloudposse/terraform-aws-ecs-container-definition?ref=0.56.0"

  container_name               = "cache"
  container_image              = var.ecs_cache_image
  container_memory_reservation = var.ecs_cache_memory
  essential                    = true
  environment                  = []

  log_configuration = {
    logDriver = "awslogs"
    options = {
      "awslogs-group"         = "ecs/cache-${var.environment}"
      "awslogs-region"        = var.region
      "awslogs-stream-prefix" = "ecs"
    }
  }
}

module "database" {
  source = "../modules/secure-container-definitions"

  container_name               = "database"
  container_image              = "${data.aws_ssm_parameter.DB_ECR_URL.value}:develop"
  container_memory_reservation = var.ecs_db_memory
  essential                    = true
  environment                  = []

  log_configuration = {
    logDriver = "awslogs"
    options = {
      "awslogs-group"         = "ecs/database-${var.environment}"
      "awslogs-region"        = var.region
      "awslogs-stream-prefix" = "ecs"
    }
  }
}

module "nginx" {
  source = "../modules/secure-container-definitions"

  container_name               = "nginx"
  container_image              = "${data.aws_ssm_parameter.NGINX_ECR_URL.value}:develop"
  container_memory_reservation = var.ecs_nginx_memory
  essential                    = true
  environment                  = []

  log_configuration = {
    logDriver = "awslogs"
    options = {
      "awslogs-group"         = "ecs/nginx-${var.environment}"
      "awslogs-region"        = var.region
      "awslogs-stream-prefix" = "ecs"
    }
  }

  links = ["php-fpm:php-fpm"]

  docker_labels = {
    "traefik.enable"                                = "true"
    "traefik.http.routers.php-fpm.rule"             = "Host(`${var.web_domain}`,`www.${var.web_domain}`,`${var.files_domain}`,`www.${var.files_domain}`)"
    "traefik.http.routers.php-fpm.tls"              = "true"
    "traefik.http.routers.php-fpm.tls.certresolver" = "mytlschallenge"
  }

  healthcheck = {
    command     = ["CMD-SHELL", "curl -f http://localhost"]
    interval    = 30
    timeout     = 5
    retries     = 3
    startPeriod = 15
  }
}

module "php-fpm" {
  source = "../modules/secure-container-definitions"

  container_name               = "php-fpm"
  container_image              = "${data.aws_ssm_parameter.PHP_ECR_URL.value}:develop"
  container_memory_reservation = var.ecs_php_memory
  essential                    = true
  environment                  = []

  log_configuration = {
    logDriver = "awslogs"
    options = {
      "awslogs-group"         = "ecs/php-fpm-${var.environment}"
      "awslogs-region"        = var.region
      "awslogs-stream-prefix" = "ecs"
    }
  }

  links = ["cache:cache", "database:database"]

  secrets = [
    {
      name      = "WIKIJUMP_URL_DOMAIN"
      valueFrom = aws_ssm_parameter.URL_DOMAIN.name
    },
    {
      name      = "WIKIJUMP_URL_UPLOAD_DOMAIN"
      valueFrom = aws_ssm_parameter.URL_UPLOAD_DOMAIN.name
    },
    {
      name      = "WIKIJUMP_DB_HOST"
      valueFrom = aws_ssm_parameter.DB_HOST.name
    }
  ]
}

module "datadog" {
  source = "../modules/secure-container-definitions"

  container_name               = "datadog"
  container_image              = "gcr.io/datadoghq/agent:7"
  container_memory_reservation = var.ecs_datadog_memory
  essential                    = false
  environment                  = []

  log_configuration = {
    logDriver = "awslogs"
    options = {
      "awslogs-group"         = "ecs/datadog-${var.environment}"
      "awslogs-region"        = var.region
      "awslogs-stream-prefix" = "ecs"
    }
  }

  secrets = [
    {
      name      = "DD_API_KEY"
      valueFrom = var.datadog_api_key
    },
    {
      name      = "DD_SITE"
      valueFrom = var.datadog_site
    }
  ]

  mount_points = [
    {
      sourceVolume  = "docker-socket"
      containerPath = "/var/run/docker.sock"
      readOnly      = true

    },
    {
      sourceVolume  = "proc"
      containerPath = "/host/proc"
      readOnly      = true
    },
    {
      sourceVolume  = "cgroup"
      containerPath = "/host/sys/fs/cgroup"
      readOnly      = true
    }
  ]
}

module "reverse-proxy" {
  source = "../modules/secure-container-definitions"

  container_name               = "reverse-proxy"
  container_image              = var.ecs_traefik_image
  container_memory_reservation = var.ecs_traefik_memory
  essential                    = true
  environment = [
    {
      name  = "AWS_ACCESS_KEY_ID"
      value = var.route53_access_key
    },
    {
      name  = "AWS_SECRET_ACCESS_KEY"
      value = var.route53_secret_key
    },
    {
      name  = "AWS_REGION"
      value = var.region
    }
  ]

  log_configuration = {
    logDriver = "awslogs"
    options = {
      "awslogs-group"         = "ecs/traefik-${var.environment}"
      "awslogs-region"        = var.region
      "awslogs-stream-prefix" = "ecs"
    }
  }

  links = ["nginx:nginx"]

  port_mappings = [
    {
      containerPort = 8081
      hostPort      = 8081
      protocol      = "tcp"
    },
    {
      containerPort = 443
      hostPort      = 443
      protocol      = "tcp"
    },
    {
      containerPort = 80
      hostPort      = 80
      protocol      = "tcp"
    }
  ]

  command = [
    "--providers.docker",
    "--entrypoints.web.address=:80",
    "--entrypoints.web.http.redirections.entryPoint.to=web-secure",
    "--entrypoints.web.http.redirections.entryPoint.scheme=https",
    "--entrypoints.web.http.redirections.entrypoint.permanent=true",
    "--entrypoints.web-secure.address=:443",
    "--certificatesresolvers.mytlschallenge.acme.dnschallenge.provider=route53",
    "--certificatesresolvers.mytlschallenge.acme.dnschallenge.delaybeforecheck=30",
    "--certificatesresolvers.mytlschallenge.acme.storage=/letsencrypt/acme.json",
    "--ping.entrypoint=ping",
    "--entrypoints.ping.address=:8081"
  ]
  mount_points = [
    {
      sourceVolume  = "docker-socket"
      containerPath = "/var/run/docker.sock"
      readOnly      = true

    },
    {
      sourceVolume  = "letsencrypt"
      containerPath = "/letsencrypt"
      readOnly      = false
    }
  ]

  container_depends_on = [
    {
      containerName = "nginx"
      condition     = "HEALTHY"
    }
  ]
}

output "cache_json" {
  description = "Container definition in JSON format"
  value       = module.cache.json_map_encoded_list
}

output "database_json" {
  description = "Container definition in JSON format"
  value       = module.database.sensitive_json_map_encoded_list
  sensitive   = true
}

output "php-fpm_json" {
  description = "Container definition in JSON format"
  value       = module.php-fpm.sensitive_json_map_encoded_list
  sensitive   = true
}

output "nginx_json" {
  description = "Container definition in JSON format"
  value       = module.nginx.sensitive_json_map_encoded_list
  sensitive   = true
}

output "reverse-proxy_json" {
  description = "Container definition in JSON format"
  value       = module.reverse-proxy.sensitive_json_map_encoded_list
  sensitive   = true
}

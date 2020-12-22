resource "aws_ecs_cluster" "wikijump-ecs" {
  name               = "wikijump-${var.environment}"
  capacity_providers = [aws_ecs_capacity_provider.asg.name]

  default_capacity_provider_strategy {
    capacity_provider = aws_ecs_capacity_provider.asg.name
    weight = 1
  }
}

resource "aws_ecs_capacity_provider" "asg" {
  name = aws_autoscaling_group.ecs_nodes.name

  auto_scaling_group_provider {
    auto_scaling_group_arn         = aws_autoscaling_group.ecs_nodes.arn
    managed_termination_protection = "DISABLED"

    managed_scaling {
      maximum_scaling_step_size = 1
      minimum_scaling_step_size = 1
      status                    = "ENABLED"
      target_capacity           = 1
    }
  }
}

resource "aws_autoscaling_group" "ecs_nodes" {
  name_prefix           = "CLUSTER_NODES_"
  max_size              = 1
  min_size              = 1
  vpc_zone_identifier   = aws_subnet.container_subnet.id
  protect_from_scale_in = false

  mixed_instances_policy {
    instances_distribution {
      on_demand_percentage_above_base_capacity = 0
    }
    launch_template {
      launch_template_specification {
        launch_template_id = aws_launch_template.node.id
        version            = "$Latest"
      }

      dynamic "override" {
        for_each = var.instance_type
        content {
          instance_type     = override.key
          weighted_capacity = 1
        }
      }
    }
  }

  lifecycle {
    create_before_destroy = true
  }
}

resource "aws_ecs_task_definition" "wikijump_task" {
  family                   = "wikijump-${var.environment}-ec2"
  container_definitions    = file("task-definitions/dev-ec2.json")
  requires_compatibilities = ["EC2"]
  network_mode             = "bridge"
  execution_role_arn       = aws_iam_role.execution.arn
  task_role_arn            = aws_iam_role.task.arn
  volume {
    name      = "docker-socket"
    host_path = "/var/run/docker.sock"
  }
  volume {
    name = "letsencrypt"

    efs_volume_configuration {
      file_system_id = aws_efs_file_system.traefik_efs.id
      root_directory = "/letsencrypt"
    }
  }
}


resource "aws_ecs_service" "wikijump" {
  name                 = "wikijump-${var.environment}-svc"
  cluster              = module.ecs_cluster.id
  task_definition      = aws_ecs_task_definition.wikijump_task.arn
  desired_count        = 1 # This will be a var as we grow
  force_new_deployment = true
  load_balancer {
    target_group_arn = aws_lb_target_group.elb_target_group_443.arn
    container_name   = "reverse-proxy"
    container_port   = 443
  }
}




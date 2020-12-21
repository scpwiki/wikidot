# ELB

resource "aws_lb" "wikijump_elb" {
    name                        = "wikijump-public-elb-${var.environment}"
    internal                    = false
    load_balancer_type          = "network"
    subnet_mapping {
        subnet_id               = aws_subnet.elb_subnet.id
        allocation_id           = aws_eip.elb_eip.id
    }
    # ip_address_type = "dualstack"
    # Enable this once stable.
    enable_deletion_protection  = false

    access_logs {
        bucket                  = aws_s3_bucket.elb_logs.bucket
        prefix                  = var.environment
        # Logging is currently OFF
        enabled                 = false
    }
}

resource "aws_lb_target_group" "elb_target_group_80" {
    name        = "wikijump-tg-80-${var.environment}"
    port        = 80
    protocol    = "TCP"
    vpc_id      = aws_vpc.wikijump_vpc.id
    target_type = "instance"
    health_check {
        enabled = true
    }
}

resource "aws_lb_target_group" "elb_target_group_443" {
    name        = "wikijump-tg-443-${var.environment}"
    port        = 443
    protocol    = "TCP"
    vpc_id      = aws_vpc.wikijump_vpc.id
    target_type = "instance"
    health_check {
        enabled = true
    }
}

resource "aws_lb_listener" "elb_listener_80" {
    load_balancer_arn       = aws_lb.wikijump_elb.arn
    port                    = 80
    protocol                = "TCP"
    default_action {
        type                = "forward"
        target_group_arn = aws_lb_target_group.elb_target_group_80.arn
    }
}

resource "aws_lb_listener" "elb_listener_443" {
    load_balancer_arn       = aws_lb.wikijump_elb.arn
    port                    = 443
    protocol                = "TCP"
    default_action {
        type                = "forward"
        target_group_arn = aws_lb_target_group.elb_target_group_443.arn
    }
}

# resource "aws_lb_listener_rule" "cloudfront_forward_80" {
#     listener_arn            = aws_lb_listener.elb_listener_80.arn
#     priority                = 100

#     action {
#         type                = "forward"
#         target_group_arn    = aws_lb_target_group.elb_target_group_80.arn
#     }

#     condition {
#         source_ip {
#           values            = ["0.0.0.0/0","::/0"]
#         }
#     }
# }

# resource "aws_lb_listener_rule" "cloudfront_forward_443" {
#     listener_arn            = aws_lb_listener.elb_listener_443.arn
#     priority                = 200

#     action {
#         type                = "forward"
#         target_group_arn    = aws_lb_target_group.elb_target_group_443.arn
#     }

#     condition {
#         source_ip {
#           values            = ["0.0.0.0/0","::/0"]
#         }
#     }
# }

# resource "aws_lb_listener_rule" "fallback" {
#     listener_arn            = aws_lb_listener.elb_listener_80.arn
#     priority                = 999

#     action {
#         type                = "fixed-response"
#         fixed_response {
#             content_type    = "text/plain"
#             message_body    = "CloudFront Token Missing"
#             status_code     = "400"
#         }
#     }

#     condition {
#         path_pattern {
#         values              = ["*"]
#         }
#     }
# }

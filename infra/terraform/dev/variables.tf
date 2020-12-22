variable "environment" {
  type = string
}

variable "web_domain" {
  type = string
}

variable "files_domain" {
  type = string
}

variable "vpc_cidr_block" {
  type = string
}

variable "elb_subnet" {
  type = string
}

variable "container_subnet" {
  type = string
}

variable "cf_auth_token" {
  type = string
}

variable "cache_num_nodes" {
  type = number
}

variable "cache_ec2_size" {
  type = string
}

variable "rds_master_password" {
  type = string
}

variable "region" {
  type = string
}

variable "instance_type" {
    type = string
}

variable "user_data" {}


variable "location" {
  default     = "Brazil South"
  description = "Azure region"
}

variable "db_username" {
  default     = "adminuser"
  description = "Database admin username"
}

variable "db_password" {
  default     = "admin1234!"
  description = "Database admin password"
  sensitive   = true
}

variable "storage_mb" {
  default     = 42600 # equivalente a 41.6 GB
  description = "Maximo almacenamiento para MySQL Flexible Server"
}

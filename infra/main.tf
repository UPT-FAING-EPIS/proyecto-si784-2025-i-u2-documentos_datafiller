provider "azurerm" {
  features {}
}

resource "azurerm_resource_group" "rg" {
  name     = "rg-infracost"
  location = var.location
}

resource "azurerm_mysql_flexible_server" "mysql" {
  name                   = "mysql-infracost-db"
  resource_group_name    = azurerm_resource_group.rg.name
  location               = azurerm_resource_group.rg.location
  administrator_login    = var.db_username
  administrator_password = var.db_password
  sku_name               = "B_Standard_B1ms"
  version = "8.0.21"

  storage {
    size_gb = var.storage_gb
  }
} 


output "db_fqdn" {
  value = azurerm_mysql_flexible_server.mysql.fqdn
}

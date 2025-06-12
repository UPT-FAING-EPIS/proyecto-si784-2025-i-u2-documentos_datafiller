provider "azurerm" {
  features {}
}

# Base de datos
resource "azurerm_resource_group" "rg_db" {
  name     = "rg-infracost-db"
  location = "Brazil South"
}

resource "azurerm_mysql_flexible_server" "mysql" {
  name                   = "mysql-infracost-db"
  resource_group_name    = azurerm_resource_group.rg_db.name
  location               = azurerm_resource_group.rg_db.location
  administrator_login    = var.db_username
  administrator_password = var.db_password
  sku_name               = "B_Standard_B1ms"
  version                = "8.0.21"

  storage {
    size_gb = var.storage_gb
  }
}

# Web App
resource "azurerm_resource_group" "rg_web" {
  name     = "rg-infracost-web"
  location = "East US"
}

resource "azurerm_app_service_plan" "app_plan" {
  name                = "appservice-plan"
  location            = azurerm_resource_group.rg_web.location
  resource_group_name = azurerm_resource_group.rg_web.name

  sku {
    tier = "Basic"
    size = "B1"
  }
}

resource "azurerm_app_service" "web_app" {
  name                = "infracost-web-app"
  location            = azurerm_resource_group.rg_web.location
  resource_group_name = azurerm_resource_group.rg_web.name
  app_service_plan_id = azurerm_app_service_plan.app_plan.id

  site_config {
    always_on = true
  }

  app_settings = {
    "DB_HOST"     = azurerm_mysql_flexible_server.mysql.fqdn
    "DB_USER"     = var.db_username
    "DB_PASSWORD" = var.db_password
  }

  https_only = true
}

# Page snapshot

```yaml
- img "Data Filler Logo"
- heading "Registro de Usuario" [level=2]
- text: "Error interno: Error de conexión: SQLSTATE[HY000] [2002] No se puede establecer una conexión ya que el equipo de destino denegó expresamente dicha conexión Nombre de Usuario"
- textbox "Nombre de Usuario"
- text: Apellido Paterno
- textbox "Apellido Paterno"
- text: Apellido Materno
- textbox "Apellido Materno"
- text: Correo Electrónico
- textbox "Correo Electrónico"
- text: Solo se permite una cuenta por email Contraseña
- textbox "Contraseña"
- text: Mínimo 6 caracteres Confirmar Contraseña
- textbox "Confirmar Contraseña"
- button "Registrarse"
- paragraph:
  - text: ¿Ya tienes una cuenta?
  - link "Inicia sesión aquí":
    - /url: login_view.php
- paragraph:
  - link "Volver al inicio":
    - /url: ../../index.php
```
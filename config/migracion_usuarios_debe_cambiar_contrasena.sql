-- Marca de "contraseña temporal" para usuarios creados automáticamente (ej. al registrar una empresa):
-- obliga a definir una nueva contraseña en el primer inicio de sesión.
ALTER TABLE usuarios
    ADD COLUMN debe_cambiar_contrasena TINYINT(1) NOT NULL DEFAULT 0 AFTER rol;

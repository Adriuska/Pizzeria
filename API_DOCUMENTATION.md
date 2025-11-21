# Pizza Nova Backend API

Backend para la aplicación móvil Pizza Nova de pedidos de pizza.

## Configuración rápida

### Datos de prueba

**Usuario de prueba:**
- Email: `test@example.com`
- Contraseña: `password123`

### Base de datos

La base de datos se configuró automáticamente con las migraciones.

## Endpoints API

### Autenticación

#### Login
```
POST /api/auth/login
Content-Type: application/json

{
  "email": "test@example.com",
  "password": "password123"
}
```

**Respuesta exitosa (200):**
```json
{
  "success": true,
  "message": "Autenticación exitosa",
  "data": {
    "id": 1,
    "email": "test@example.com",
    "name": "Test User",
    "token": "abc123def456..."
  }
}
```

**Respuesta de error (401):**
```json
{
  "error": "INVALID_CREDENTIALS",
  "message": "Email o contraseña incorrectos",
  "code": 401
}
```

#### Recuperación de contraseña
```
POST /api/auth/forgot-password
Content-Type: application/json

{
  "email": "test@example.com"
}
```

**Respuesta (200):**
```json
{
  "success": true,
  "message": "Si el email existe, recibirá un correo de recuperación",
  "data": {
    "reset_token": "xyz789..."
  }
}
```

### Menú

#### Obtener todas las pizzas
```
GET /api/menu/pizzas
```

**Respuesta (200):**
```json
{
  "success": true,
  "message": "Pizzas obtenidas correctamente",
  "data": [
    {
      "id": 1,
      "name": "Margherita",
      "description": "Pizza clásica con tomate, queso mozarela y albahaca fresca",
      "ingredients": ["Tomate", "Mozarela", "Albahaca", "Aceite de oliva"],
      "sizes": [
        {
          "size": "small",
          "price": "8.99",
          "diameter": "25cm",
          "currency": "USD"
        },
        {
          "size": "medium",
          "price": "12.99",
          "diameter": "30cm",
          "currency": "USD"
        },
        {
          "size": "large",
          "price": "16.99",
          "diameter": "35cm",
          "currency": "USD"
        }
      ],
      "image_url": "https://via.placeholder.com/300?text=Margherita",
      "category": "Clásicas",
      "is_available": true
    }
  ]
}
```

#### Obtener categorías
```
GET /api/menu/categories
```

**Respuesta (200):**
```json
{
  "success": true,
  "message": "Categorías obtenidas correctamente",
  "data": ["Carnes", "Clásicas", "Tropical", "Vegetariana"]
}
```

### Órdenes

#### Crear orden
```
POST /api/orders
Content-Type: application/json

{
  "user_id": 1,
  "items": [
    {
      "pizza_id": 1,
      "size": "medium",
      "quantity": 2
    },
    {
      "pizza_id": 2,
      "size": "large",
      "quantity": 1
    }
  ]
}
```

**Respuesta exitosa (201):**
```json
{
  "success": true,
  "message": "Pedido creado exitosamente",
  "data": {
    "id": 1,
    "user_id": 1,
    "status": "pending",
    "subtotal_amount": "47.97",
    "tax_amount": "10.07",
    "total_amount": "58.04",
    "items": [
      {
        "id": 1,
        "pizza_id": 1,
        "pizza_name": "Margherita",
        "size": "medium",
        "quantity": 2,
        "unit_price": "12.99",
        "item_subtotal": "25.98",
        "tax_rate": "0.21",
        "tax_amount": "5.46",
        "item_total": "31.44"
      },
      {
        "id": 2,
        "pizza_id": 2,
        "pizza_name": "Pepperoni",
        "size": "large",
        "quantity": 1,
        "unit_price": "17.99",
        "item_subtotal": "17.99",
        "tax_rate": "0.21",
        "tax_amount": "3.78",
        "item_total": "21.77"
      }
    ],
    "created_at": "2025-11-21T11:41:19+00:00",
    "updated_at": "2025-11-21T11:41:19+00:00",
    "estimated_delivery_time": null
  }
}
```

**Respuesta de error - Orden vacía (400):**
```json
{
  "error": "EMPTY_ORDER",
  "message": "El pedido no puede estar vacío",
  "code": 400
}
```

**Respuesta de error - Pizza no disponible (404):**
```json
{
  "error": "PIZZA_NOT_AVAILABLE",
  "message": "La pizza seleccionada no está disponible",
  "code": 404
}
```

#### Obtener orden actual (pendiente)
```
GET /api/orders/current?user_id=1
```

**Respuesta (200):**
```json
{
  "success": true,
  "message": "Pedido obtenido correctamente",
  "data": {
    "id": 1,
    "user_id": 1,
    "status": "pending",
    "subtotal_amount": "47.97",
    "tax_amount": "10.07",
    "total_amount": "58.04",
    "items": [...],
    "created_at": "2025-11-21T11:41:19+00:00",
    "updated_at": "2025-11-21T11:41:19+00:00",
    "estimated_delivery_time": null
  }
}
```

#### Actualizar orden
```
PUT /api/orders/{id}
Content-Type: application/json

{
  "status": "confirmed",
  "estimated_delivery_time": "2025-11-21T12:00:00+00:00"
}
```

**Respuesta (200):**
```json
{
  "success": true,
  "message": "Pedido actualizado exitosamente",
  "data": { ... }
}
```

Estados válidos: `pending`, `confirmed`, `preparing`, `ready`, `delivered`, `cancelled`

#### Eliminar orden
```
DELETE /api/orders/{id}
```

**Respuesta (200):**
```json
{
  "success": true,
  "message": "Pedido eliminado exitosamente"
}
```

**Nota:** Solo se pueden eliminar órdenes con estado `pending`

## Códigos de respuesta

- **200 OK** - Operación exitosa
- **201 Created** - Recurso creado exitosamente
- **400 Bad Request** - Datos inválidos o validación fallida
- **401 Unauthorized** - Credenciales inválidas
- **404 Not Found** - Recurso no encontrado
- **500 Internal Server Error** - Error del servidor

## Estructura de errores

### Validación
```json
{
  "error": "VALIDATION_ERROR",
  "message": "Datos de entrada inválidos",
  "details": {
    "email": "El email es requerido",
    "password": "La contraseña debe tener al menos 6 caracteres"
  },
  "code": 400
}
```

### Credenciales inválidas
```json
{
  "error": "INVALID_CREDENTIALS",
  "message": "Email o contraseña incorrectos",
  "code": 401
}
```

### Recurso no encontrado
```json
{
  "error": "PIZZA_NOT_FOUND",
  "message": "Pizza no encontrada",
  "code": 404
}
```

## Prueba rápida con cURL

### Login
```bash
curl -X POST http://localhost:8000/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{"email":"test@example.com","password":"password123"}'
```

### Obtener pizzas
```bash
curl http://localhost:8000/api/menu/pizzas
```

### Crear orden
```bash
curl -X POST http://localhost:8000/api/orders \
  -H "Content-Type: application/json" \
  -d '{
    "user_id": 1,
    "items": [
      {"pizza_id": 1, "size": "medium", "quantity": 1}
    ]
  }'
```

## Notas importantes

1. **Formato de precios**: Los precios se devuelven como strings con 2 decimales (ej: "12.99")
2. **Impuesto**: El impuesto se calcula automáticamente al 21%
3. **Tamaños de pizza**: Los tamaños disponibles varían por pizza y se incluyen en el objeto de la pizza
4. **Validación**: Todos los campos requeridos se validan antes de procesar
5. **Gestión de errores**: Siempre verifica el campo `error` en la respuesta para determinar si hubo un problema

## Modelos de datos

### User
- `id` (int): ID único del usuario
- `email` (string): Email único del usuario
- `password` (string): Contraseña hasheada
- `name` (string): Nombre del usuario
- `phone` (string): Teléfono del usuario
- `address` (string): Dirección de entrega
- `token` (string): Token único para recuperación de contraseña
- `roles` (array): Roles del usuario

### Pizza
- `id` (int): ID único
- `name` (string): Nombre de la pizza
- `description` (string): Descripción detallada
- `ingredients` (string): Lista de ingredientes separados por comas
- `sizes` (array): Array de objetos con propiedades: size, price, diameter, currency
- `image_url` (string): URL de la imagen
- `category` (string): Categoría de la pizza
- `is_available` (boolean): Disponibilidad

### Order
- `id` (int): ID único del pedido
- `user_id` (int): ID del usuario propietario
- `status` (string): Estado del pedido (pending, confirmed, preparing, ready, delivered, cancelled)
- `subtotal_amount` (decimal): Subtotal sin impuestos
- `tax_amount` (decimal): Monto de impuestos
- `total_amount` (decimal): Total con impuestos
- `items` (array): Array de OrderItem
- `created_at` (datetime): Fecha de creación
- `updated_at` (datetime): Fecha de última actualización
- `estimated_delivery_time` (datetime): Tiempo estimado de entrega

### OrderItem
- `id` (int): ID único
- `pizza_id` (int): ID de la pizza
- `pizza_name` (string): Nombre de la pizza
- `size` (string): Tamaño seleccionado
- `quantity` (int): Cantidad de pizzas
- `unit_price` (decimal): Precio unitario
- `item_subtotal` (decimal): Subtotal del item
- `tax_rate` (decimal): Tasa de impuesto aplicada
- `tax_amount` (decimal): Monto de impuesto del item
- `item_total` (decimal): Total del item con impuestos

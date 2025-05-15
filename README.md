# Docker Commands
 - cp .env.example .env
 - dokcer-compose build
 - docker-compose up -d (run in background)
 - docker-compose down
 - docker-compose ps (list of all docker containers)
 - docker images (list of all docker images)
 - docker logs containername


# Get inside app-test container 
 - docker exec -it test-app bash

# And run the following commands
 - php artisan migrate --seed (add migrate:fresh if already exists)
 - php artisan app:elasticsearch-mapping (create new index and add explicit mapping)

# Data format to store product
# Method: POST, route: /api/products
# Content: application/json
{
  "name": "iPhone 15",
  "description": "Latest model",
  "price": 1299.99,
  "in_stock": true,
  "category": "electronics",
  "latitude": 37.7749,
  "longitude": -122.4194,
  "tags": ["apple", "smartphone", "ios"],
  "attributes": [
    { "name": "color", "value": "black" },
    { "name": "storage", "value": "256GB" }
  ]
}
 

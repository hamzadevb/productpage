### Network:
    docker network create smartep-shared-network

### Dev:
    docker compose up --wait
### Prod:
    docker compose -f compose.yaml -f compose.prod.yaml up --wait

### FakeData (inside php container) :
    php bin/console d:f:l

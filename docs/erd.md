# Diagrama De Datos / DER

```mermaid
erDiagram
    USERS {
        bigint id PK "NOT NULL"
        string name "NOT NULL"
        string email UK "NOT NULL"
        timestamp email_verified_at "NULL"
        string password "NOT NULL"
        string remember_token "NULL"
        timestamp created_at "NULL"
        timestamp updated_at "NULL"
    }

    ACCESS_TOKENS {
        bigint id PK "NOT NULL"
        bigint user_id FK "NOT NULL"
        string token_hash UK "NOT NULL"
        timestamp expires_at "NOT NULL"
        timestamp created_at "NULL"
        timestamp updated_at "NULL"
    }

    FAVORITE_GIFS {
        bigint id PK "NOT NULL"
        string gif_id "NOT NULL"
        string alias "NOT NULL"
        bigint user_id FK "NOT NULL"
        timestamp created_at "NULL"
        timestamp updated_at "NULL"
    }

    AUDIT_LOGS {
        bigint id PK "NOT NULL"
        string service "NOT NULL"
        string method "NOT NULL"
        json request_body "NULL"
        text response_body "NULL"
        smallint status_code "NOT NULL"
        string ip "NULL"
        bigint user_id FK "NULL"
        timestamp created_at "NULL"
        timestamp updated_at "NULL"
    }

    USERS ||--o{ ACCESS_TOKENS : has
    USERS ||--o{ FAVORITE_GIFS : saves
    USERS ||--o{ AUDIT_LOGS : performs
```

## Notas

- `access_tokens.token_hash` almacena el hash `sha256` del token plano.
- `favorite_gifs` tiene una restriccion unica por `user_id` + `gif_id`.
- `audit_logs.user_id` es nullable porque el login se audita antes de existir usuario autenticado en la request.
- `request_body` se guarda como JSON sanitizado.
- `response_body` se guarda como texto raw truncado.

## Indices Y Restricciones

- `users.email`: indice unico para login por email.
- `access_tokens.user_id`: indice por foreign key hacia `users.id`.
- `access_tokens.token_hash`: indice unico para validar tokens de forma eficiente.
- `favorite_gifs.user_id`: indice por foreign key hacia `users.id`.
- `favorite_gifs.user_id + favorite_gifs.gif_id`: indice unico para evitar favoritos duplicados por usuario.
- `audit_logs.user_id`: indice por foreign key hacia `users.id`, nullable para requests sin usuario autenticado.

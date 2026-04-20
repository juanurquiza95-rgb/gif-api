# Diagrama De Casos De Uso

```mermaid
flowchart LR
    usuario["Usuario"]

    subgraph sistema["GIF API"]
        login["Loguearse"]
        buscar["Buscar GIFs"]
        obtener["Obtener GIF por ID"]
        guardar["Guardar GIF como favorito"]
    end

    usuario --> login
    usuario --> buscar
    usuario --> obtener
    usuario --> guardar
```

## Descripcion

- El usuario puede loguearse para obtener acceso a la API.
- El usuario puede buscar GIFs por un criterio de busqueda.
- El usuario puede obtener el detalle de un GIF por ID.
- El usuario puede guardar un GIF como favorito.

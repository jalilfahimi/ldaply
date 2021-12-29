# MULTA MEDIO Kitchen Planner

Export your local logs in this folder. This folder is ignored by git. 

### Export logs from a container:

```
sudo docker logs {container-id or container-fullname} > data/logs/{file-name}.log 2>&1
```

**Note:** Use the command without the curly braces.<br />
Example: 

```
sudo docker logs ff692b3e6ac1 > data/logs/kitchenplanner-server.log 2>&1
sudo docker logs kitchenplanner_kitchenplanner-server_1 > data/logs/servicehub-server.log 2>&1
```

## Contanct
[multamedio.de](https://multamedio.de/kontakt)

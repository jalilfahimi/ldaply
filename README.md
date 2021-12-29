# ldaply

## Requirements
In order to use this software, you need to install docker. <br />
Links:<br />
[Debian](https://docs.docker.com/engine/install/debian/)<br />
[Ubuntu](https://docs.docker.com/engine/install/ubuntu/)<br />

## Usage
### After installing and setting up the initial steps, you are now ready to start using this software.

Navigate to the main directory and set the environment:

```
With bash:

sudo bash env.sh 'development'
sudo bash env.sh 'production'

```

OR

```
With sh:

sudo sh env.sh 'development'
sudo sh env.sh 'production'

```

#### In order to start using this software, you only need to start the docker:
```
sudo docker-compose up
```
**Note:** Use **--build** to create a new image without changing the image version and prevent caching issues:<br />
```
sudo docker-compose up --build
```

#### In order to stop the docker, run the following command:
Stop the docker:
```
sudo docker-compose down
```
**Note:** Use **--remove-orphans** to stop cached images:<br />
```
sudo docker-compose down --remove-orphans
```
### Passwords
See the **passwords.passwd** file for the credentials.<br />

### Links
ldaply:    http://0.0.0.0:40002
<br/>


### Useful commands
#### Images

List images:
```
sudo docker ps
```
**Note:** Use -a to list all images:<br />
```
sudo docker ps -a
```
Log into an image:
```
sudo docker exec -it $ID /bin/bash/
```

#### Volumes

List volumes:
```
sudo docker volume ls
```
Remove a volume:
```
sudo docker volume rm $ID
```
Remove all volumes:
```
sudo docker volume prune
```

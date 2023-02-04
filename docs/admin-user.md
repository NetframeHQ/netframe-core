Connect as super admin user
===========================

How to connect
--------------

Add `AUTO_ADMIN_CONNECT=true` in `.env` and restart Docker Compose Netframe service.

```sh
echo "AUTO_ADMIN_CONNECT=true" >> .env
docker-compose restart netframe
```

Then you can open the global management URL (`/management`).

How to go back to normal
------------------------

Remove the `AUTO_ADMIN_CONNECT=true` line from `.env` and restart Docker Compose Netframe service.

```sh
sed -i "/AUTO_ADMIN_CONNECT/d" .env
docker-compose restart netframe
```

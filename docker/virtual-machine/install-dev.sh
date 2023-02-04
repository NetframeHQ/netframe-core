#!/usr/bin/env sh

export DEBIAN_FRONTEND=noninteractive

# prepare share
SHARE_PATH="$(grep "netframe:" /etc/passwd | cut -d ":" -f 6)/share"
mkdir -m 775 -p "${SHARE_PATH}"
chown -R netframe:netframe "${SHARE_PATH}"
# NFS
echo "/home/netframe/share *(rw,async,no_subtree_check,root_squash,all_squash,anonuid=1000,anongid=1000)" >> /etc/exports
# SMB
printf "netframe\nnetframe\n" | smbpasswd -a -s netframe
cat > /etc/samba/smb.conf << EOF
[global]
workgroup = NETFRAME
netbios name = devdocker
server string = %h server
security = user
unix password sync = yes

[netframe]
path = /home/netframe/share
read only = no
browseable = yes
writeable = yes
valid users = netframe
write list = netframe
create mask = 0775
directory = 775
force user = netframe
force group = netframe
EOF

# install Docker
curl -fsSL https://download.docker.com/linux/debian/gpg | apt-key add -
add-apt-repository "deb [arch=amd64] https://download.docker.com/linux/debian $(lsb_release -cs) stable"
apt-get update
apt-get install -yq docker-ce docker-ce-cli containerd.io
# install Docker Compose
curl -L "https://github.com/docker/compose/releases/download/1.27.4/docker-compose-$(uname -s)-$(uname -m)" -o /usr/bin/docker-compose
chmod +x /usr/bin/docker-compose

usermod -aG docker netframe # allow netframe to use Docker without sudo

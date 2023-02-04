FROM debian:buster

RUN apt-get update
RUN apt-get install -y dnsmasq

CMD ["/usr/sbin/dnsmasq", "-C", "/etc/dnsmasq.conf", "--log-facility=-", "-k"]

#! /bin/ash
# Before executing this script make sure to install these modules insmod veth.ko and insmod sch_tbf.ko ( /lib/modules/3.3.8/)
# and restart the network service (/etc/init.d/network restart)

# create the two OVS
ovs-vsctl add-br br1
sleep 1
ovs-vsctl add-br br2
sleep 1

#remove all the forwarding rules for the switches
ovs-ofctl del-flows br1
sleep 1
ovs-ofctl del-flows br2
sleep 1


#adding the LAN and the Wlan interface to the first OVS
ovs-vsctl add-port br1 eth0.2
sleep 1
ovs-vsctl add-port br1 eth0.3
sleep 1
ovs-vsctl add-port br1 wlan0
sleep 1


#adding the WAN interface to the second OVS
ovs-vsctl add-port br2 eth1
sleep 1



# create a eight virtual interfaces (the command create tow virtual interface  veth0 and veth1 )

ip link add type veth
sleep 1

ip link add type veth
sleep 1

ip link add type veth
sleep 1

ip link add type veth
sleep 1


# make sure all the physical and virtual interface are up and in promisc
ifconfig veth0 up
ifconfig veth1 up
ifconfig veth2 up
ifconfig veth3 up
ifconfig veth4 up
sleep 1
ifconfig veth5 up
ifconfig veth6 up
ifconfig veth7 up
ifconfig eth0 up
ifconfig eth1 up
ifconfig wlan0 up
sleep 1


ifconfig veth0 promisc
ifconfig veth1 promisc
ifconfig veth2 promisc
ifconfig veth3 promisc
sleep 1
ifconfig veth4 promisc
ifconfig veth5 promisc
ifconfig veth6 promisc
ifconfig veth7 promisc
sleep 1
ifconfig eth0 promisc
ifconfig eth1 promisc
ifconfig wlan0 promisc
sleep 1


#adding the virtual interfaces to the two OVS and make sure they are peered

ovs-vsctl set interface veth0 type=patch
ovs-vsctl set interface veth0 options:peer=veth1
sleep 1
ovs-vsctl set interface veth1 type=patch
ovs-vsctl set interface veth1 options:peer=veth0
sleep 1
ovs-vsctl set interface veth2 type=patch
ovs-vsctl set interface veth2 options:peer=veth3
sleep 1
ovs-vsctl set interface veth3 type=patch
ovs-vsctl set interface veth3 options:peer=veth2
sleep 1
ovs-vsctl set interface veth4 type=patch
ovs-vsctl set interface veth4 options:peer=veth5
sleep 1
ovs-vsctl set interface veth5 type=patch
ovs-vsctl set interface veth5 options:peer=veth4
sleep 1
ovs-vsctl set interface veth6 type=patch
ovs-vsctl set interface veth6 options:peer=veth7
sleep 1
ovs-vsctl set interface veth7 type=patch
ovs-vsctl set interface veth7 options:peer=veth6
sleep 1

ovs-vsctl add-port br1 veth0
ovs-vsctl add-port br1 veth2
sleep 1
ovs-vsctl add-port br1 veth4
ovs-vsctl add-port br1 veth6
sleep 1

ovs-vsctl add-port br2 veth1
ovs-vsctl add-port br2 veth3
sleep 1

ovs-vsctl add-port br2 veth5
ovs-vsctl add-port br2 veth7
sleep 1



# Enable Spanning tree for both OVS
ovs-vsctl --no-wait set bridge br1 stp_enable=true
ovs-vsctl --no-wait set bridge br2 stp_enable=true
sleep 1

#adding the controller IP address to both virtual switches

ovs-vsctl set-controller br1 tcp:192.168.142.50:6633
ovs-vsctl set-fail-mode br1 secure
sleep 1
ovs-vsctl set-controller br2 tcp:192.168.142.50:6633
ovs-vsctl set-fail-mode br2 secure
sleep 1

# Rate limiting the bandwidth between the two OVS both virtual interfaces
# 5 Mbit to web, 10 Mbit to Video, 15 Mbit to P2P and 4 to VoIP (all both upload and download)
################# In case of tbf Qdisc ############################

tc qdisc add dev veth0 root tbf rate 5Mbit burst 10kb latency 5ms mtu 100000
sleep 1
tc qdisc add dev veth1 root tbf rate 5Mbit burst 10kb latency 5ms mtu 100000
sleep 1
tc qdisc add dev veth2 root tbf rate 10Mbit burst 10kb latency 5ms mtu 100000
sleep 1
tc qdisc add dev veth3 root tbf rate 10Mbit burst 10kb latency 5ms mtu 100000
sleep 1
tc qdisc add dev veth4 root tbf rate 15Mbit burst 10kb latency 5ms mtu 100000
sleep 1
tc qdisc add dev veth5 root tbf rate 15Mbit burst 10kb latency 5ms mtu 100000
sleep 1
tc qdisc add dev veth6 root tbf rate 4Mbit burst 10kb latency 5ms mtu 100000
sleep 1
tc qdisc add dev veth7 root tbf rate 4Mbit burst 10kb latency 5ms mtu 100000


################# In case of htb Qdisc ############################
#tc qdisc del dev veth0 root
#tc qdisc add dev veth0 root handle 1: htb default 6
#tc class add dev veth0 parent 1: classid 1:1 tbf rate 1Mbit burst 0

#tc qdisc del dev veth1 root
#tc qdisc add dev veth1 root handle 1: htb default 6
#tc class add dev veth1 parent 1: classid 1:1 tbf rate 1Mbit burst 0

#Do the same for Veth2,....7

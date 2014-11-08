FlowQoS
===============
FlowQoS is an SDN-based approach for application-based bandwidth allocation where users can allocate upstream and downstream bandwidths for different applications at a high level, offloading application identification to an SDN controller that dynamically installs traffic shaping rules for application flows. FlowQoS makes it easier for a typical user to configure priorities and facilitates more sophisticated per-flow application-based QoS, but doing so imposes its own set of challenges

In this Github you can download the FlowQoS source code and the Openwrt with OpenVswitch kernel module for Netgear WNDR3800.
To create the Dual OVS topology run dualovs-creation-script.sh but first you need to insert these two modules on Openwrt:  veth.ko and sch_tbf.ko
You need to install POX controller and the wrapped version of Libprotoident in order to run the code.
POX : https://github.com/noxrepo/pox.git
Libprotoident : https://github.com/sdonovan1985/libprotoident.git (clone, install swig and then make)

To run the code ./pox.py log.level --DEBUG flowqos.FlowQos flowqos.dns_spy


#!/bin/bash
function maxCpuLoad {
    # get load of all cpus
    # snmpwalk -v2c -cmdaf localhost HOST-RESOURCES-MIB::hrProcessorLoad
    # HOST-RESOURCES-MIB::hrProcessorLoad.196608 = INTEGER: 1
    # HOST-RESOURCES-MIB::hrProcessorLoad.196609 = INTEGER: 1
    # HOST-RESOURCES-MIB::hrProcessorLoad.196610 = INTEGER: 1
    # HOST-RESOURCES-MIB::hrProcessorLoad.196611 = INTEGER: 1
    # HOST-RESOURCES-MIB::hrProcessorLoad.196612 = INTEGER: 6
    # HOST-RESOURCES-MIB::hrProcessorLoad.196613 = INTEGER: 1
    # HOST-RESOURCES-MIB::hrProcessorLoad.196614 = INTEGER: 1
    # HOST-RESOURCES-MIB::hrProcessorLoad.196615 = INTEGER: 1
    # HOST-RESOURCES-MIB::hrProcessorLoad.196616 = INTEGER: 1
    # HOST-RESOURCES-MIB::hrProcessorLoad.196617 = INTEGER: 27
    # HOST-RESOURCES-MIB::hrProcessorLoad.196618 = INTEGER: 4
    # HOST-RESOURCES-MIB::hrProcessorLoad.196619 = INTEGER: 0
    # HOST-RESOURCES-MIB::hrProcessorLoad.196620 = INTEGER: 1
    # HOST-RESOURCES-MIB::hrProcessorLoad.196621 = INTEGER: 0
    # HOST-RESOURCES-MIB::hrProcessorLoad.196622 = INTEGER: 0
    # HOST-RESOURCES-MIB::hrProcessorLoad.196623 = INTEGER: 1
    # and get maximum value only
    snmpwalk -v2c -cmdaf localhost HOST-RESOURCES-MIB::hrProcessorLoad|cut -f 4 -d' '|sort -n -r|head -n1
}

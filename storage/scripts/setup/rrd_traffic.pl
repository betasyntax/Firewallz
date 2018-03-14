#!/usr/bin/perl
#
# copyright Martin Pot 2003-2016
# http://martybugs.net/linux/rrdtool/traffic.cgi
#
# rrd_traffic.pl

use RRDs;

# define location of rrdtool databases
my $rrd = '/var/lib/rrd';
# define location of images
my $img = '/var/www/html/rrdtool';

# process data for each interface (add/delete as required)
&ProcessInterface("eth0", "local network");
&ProcessInterface("eth1", "internet gateway");
# &ProcessInterface("eth2", "MartinMast wireless link");
# &ProcessInterface("eth3", "home wireless");

sub ProcessInterface
{
# process interface
# inputs: $_[0]: interface name (ie, eth0/eth1/eth2/ppp0)
#     $_[1]: interface description 

    # get network interface info
    my $in = `ifconfig $_[0] |grep bytes|cut -d":" -f2|cut -d" " -f1`;
    my $out = `ifconfig $_[0] |grep bytes|cut -d":" -f3|cut -d" " -f1`;

    # remove eol chars
    chomp($in);
    chomp($out);

    print "$_[0] traffic in, out: $in, $out\n";

    # if rrdtool database doesn't exist, create it
    #  if the rrdtool database was created with an older version of this script, without the MAX RRAs, you can add them using:
    #    rrdtool tune /usr/lib/rrd/filename.rrd RRA:MAX:0.5:1:576 RRA:MAX:0.5:6:672 RRA:MAX:0.5:24:732 RRA:MAX:0.5:144:1460
    #  if the rrdtool database was created with an older version of this script with explicit maximum values, change them using:
    #    rrdtool tune /usr/lib/rrd/filename.rrd --maximum in:U --maximum out:U
    if (! -e "$rrd/$_[0].rrd")
    {
        print "creating rrd database for $_[0] interface...\n";
        RRDs::create "$rrd/$_[0].rrd",
            "-s", "300",
            "DS:in:DERIVE:600:0:U",
            "DS:out:DERIVE:600:0:U",
            "RRA:AVERAGE:0.5:1:576",
            "RRA:MAX:0.5:1:576",
            "RRA:AVERAGE:0.5:6:672",
            "RRA:MAX:0.5:6:672",
            "RRA:AVERAGE:0.5:24:732",
            "RRA:MAX:0.5:24:732",
            "RRA:AVERAGE:0.5:144:1460",
            "RRA:MAX:0.5:144:1460";
        # check for database creation error
        if ($ERROR = RRDs::error) { print "$0: unable to create $rrd/$_[0].rrd: $ERROR\n"; }
    }

    # insert values into rrd
    RRDs::update "$rrd/$_[0].rrd",
        "-t", "in:out",
        "N:$in:$out";
    # check for database insertion error
    if ($ERROR = RRDs::error) { print "$0: unable to insert data into $rrd/$_[0].rrd: $ERROR\n"; }

    # create traffic graphs
    &CreateGraph($_[0], "hour", $_[1]);
    &CreateGraph($_[0], "day", $_[1]);
    &CreateGraph($_[0], "week", $_[1]);
    &CreateGraph($_[0], "month", $_[1]); 
    &CreateGraph($_[0], "year", $_[1]);
}

sub CreateGraph
{
# creates graph
# inputs: $_[0]: interface name (ie, eth0/eth1/eth2/ppp0)
#     $_[1]: interval (ie, day, week, month, year)
#     $_[2]: interface description 

    RRDs::graph "$img/$_[0]-$_[1].png",
        "-s -1$_[1]",
        "-t traffic on $_[0] :: $_[2]",
        "--lazy",
        "-h", "80", "-w", "600",
        "-l 0",
        "-a", "PNG",
        "-v bytes/sec",
        "--slope-mode",
        "--border", "0",
        "--color", "BACK#ffffff",
        "--color", "CANVAS#ffffff",
        "--font", "LEGEND:7",
        "DEF:in=$rrd/$_[0].rrd:in:AVERAGE",
        "DEF:maxin=$rrd/$_[0].rrd:in:MAX",
        "DEF:out=$rrd/$_[0].rrd:out:AVERAGE",
        "DEF:maxout=$rrd/$_[0].rrd:out:MAX",
        "CDEF:out_neg=out,-1,*",
        "CDEF:maxout_neg=maxout,-1,*",
        "AREA:in#32CD32:Incoming",
        "LINE1:maxin#336600",
        "GPRINT:in:MAX:  Max\\: %6.1lf %s",
        "GPRINT:in:AVERAGE: Avg\\: %6.1lf %S",
        "GPRINT:in:LAST: Current\\: %6.1lf %SBytes/sec\\n",
        "AREA:out_neg#4169E1:Outgoing",
        "LINE1:maxout_neg#0033CC",
        "GPRINT:maxout:MAX:  Max\\: %6.1lf %S",
        "GPRINT:out:AVERAGE: Avg\\: %6.1lf %S",
        "GPRINT:out:LAST: Current\\: %6.1lf %SBytes/sec\\n",
        "HRULE:0#000000";
    # check for graph creation error
    if ($ERROR = RRDs::error) { print "$0: unable to generate $_[0] graph: $ERROR\n"; }
}
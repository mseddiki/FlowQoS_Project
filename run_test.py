from mapper import *

def testSearch(mapper, huge_ass_domain_name):
    print huge_ass_domain_name
    print 'TYPE: ' +  mapper.searchType(huge_ass_domain_name) + '\n'


if __name__ == "__main__":
    # create mapper object with REGEX based search
    m = Mapper(1)

    print "Now test some real domains which are seen in dns responses when\
    watching a video on youtube \n\n"

    # search for random stuff
    testSearch(m, ['pagead46.l.doubleclick.net'])
    testSearch(m, ['linkhelp.clients.google.com'])
    testSearch(m, ['googlehosted.l.googleusercontent.com'])
    testSearch(m, ['daisy.ubuntu.com'])
    testSearch(m, ['ubuntu.com'])
    testSearch(m, ['r13.sn-p5qlsnle.googlevideo.com'])
    testSearch(m, ['clients.l.google.com'])
    testSearch(m, ['clients.l.google.com', 'r13.sn-p5qlsnle.googlevideo.com', 'linkhelp.clients.google.com'])
    testSearch(m, ['daisy.ubuntu.com','r13.sn-p5qlsnle.googlevideo.com', 'blahblahblah.googlevideo.com', 'shitelerandome.randomshitnle.googlevideo.com', 'clients.l.google.com'])
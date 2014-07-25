import re
from collections import defaultdict
import os
cwd = os.getcwd() 
absPath = cwd + '/pox/flowqos/servicedef/'
#absPath = 'servicedef/'

class MapperException(Exception):
  pass


class Mapper:
  def __init__(self, DNS_SEARCH_REGEX = 1):
    self.name_to_service = defaultdict(lambda: 'DEFAULT')


    if DNS_SEARCH_REGEX:
      self.mode = 'REGEX'
    else:
      self.mode='WEB'
      MapperException("Select REGEX mode. No other mode available")

    self.loadTypeFiles()

  def loadTypeFiles(self):
    self.loadFile(absPath + 'adverts.ini', 'ADVERT')
    self.loadFile(absPath + 'background.ini', 'BACKGROUND')
    self.loadFile(absPath + 'video.ini', 'VIDEO')
    self.loadFile(absPath + 'web.ini', 'WEB')

  def loadFile(self, filename, service):
    f = open(filename, 'r')
    for line in f.readlines():
      self.name_to_service[(line.strip('\n').replace('*', '[\S]*'))] = service

  def createTypePoll(self):
    """For every new search, poll the type with the most
    matches instead of the first match"""
    self.types_poll = defaultdict(int)
    self.types_poll['WEB'] = 0

  def searchType(self, dnsname):
    """Turns out, dnsname is a list. It can have 1 or more elements
    So again, poll across all dnsnames - the type with the highest
    number of queries is returned
    
    a better way to do this might be giving priorities, for example
    if dnsname = [<other>, <other>, <other>, <video>, <other>] then
    return VIDEO instead of OTHER (which is returned on simple
    polling"""
    self.createTypePoll()
    for each_domain in dnsname:
      for name, service in self.name_to_service.items():
        found = re.search(name, each_domain)
        if found is not None:
          # if match is found, increment counter of that TYPE
          self.types_poll[service]+=1
    return max(self.types_poll, key=self.types_poll.get)

  def searchTypeByStringMatching(self, dnsname):
    self.createTypePoll()
    for name, service in self.name_to_service.items():
      if name in dnsname:
        self.types_poll[service] += 1
        continue
    return max(self.types_poll, key=self.types_poll.get)

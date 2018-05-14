#!/usr/bin/python2.7
#coding: utf-8
import sys
reload(sys)
sys.setdefaultencoding('utf-8')
from common import XLSXDictReader
medlemmer = []

data = XLSXDictReader('medlemmer.xlsx')

for row in data:
    user = {}
    user['userid'] = row['PersonId']
    user['fullname'] =  u"{} {}".format(row['Fornavn'], row['Etternavn'])
    user['phonemobile'] = row['Tlf. mobil'] 
    medlemmer.append(user)

print json.dumps(medlemmer, ensure_ascii=False, encoding='utf8')


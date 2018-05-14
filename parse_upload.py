#!/usr/bin/python2.7
#coding: utf-8
"""Receive a filename and return a json object

    The file must be a modern (xlsx) Excel spreadsheet.

    The script parses the first sheet and returns the
    data as a JSON-serialized list of dicts.
"""
import json
import sys
from common import xl_read_as_dict
reload(sys)
sys.setdefaultencoding('utf-8')
MEM = []

DAT = xl_read_as_dict('MEM.xlsx')

for row in DAT:
    user = {}
    user['userid'] = row['PersonId']
    user['fullname'] = u"{} {}".format(row['Fornavn'], row['Etternavn'])
    user['phonemobile'] = row['Tlf. mobil']
    MEM.append(user)

print json.dumps(MEM, ensure_ascii=False, encoding='utf8')

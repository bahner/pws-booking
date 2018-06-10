#!/usr/bin/python2.7
#coding: utf-8
"""Receive a filename and return a json object

    The file must be a modern (xlsx) Excel spreadsheet.

    The script parses the first sheet and returns the
    data as a JSON-serialized list of dicts.
"""
import json
import sys
import re

from validate_email import validate_email

from common import xl_read_as_dict
from constants import ATTR_MAP as attribute_map, DEFAULT_EMAIL

# Reload sys to set encoding to utf-8. This is important or else the
# database will be corrupted!
reload(sys)
sys.setdefaultencoding('utf-8')

# Constants
MEMBERS = []
FILE = sys.argv[1]

# Read input file (spreadsheet)
DATA = xl_read_as_dict(FILE)

# How to translate the headers in the Excel sheet.
# This is likely to change ....

# NB! Match this against $COLUMNS in pws-booking.
# fullname is a special cases!
def gen_user_from_row(user_row):
    """Reads a row, maps the user attributes and returns a user dict

        Ref. mysql@opk.no:/opk/opk_booking_user_import
    """

    # Define individual user.
    # Note that PHP uses the first key of the array as a primary key
    # for the object in the database :( That's the way it is. The
    # user dictionary MUST be an OrderedDict

    # If lookups in the attribute map fails this should fail to
    user = {}

    # Generate fullname
    user['fullname'] = u"{} {}".format(
        user_row[attribute_map['firstname']],
        user_row[attribute_map['lastname']],
    )

    # This is supposed to be a list of *active* members, but there is no
    # attribute for this is NIFs database, so we set it here:
    user['status'] = 'active'

    # Read attributes we are likely to need.
    for opkkey, nifkey in attribute_map.items():

        # Handle emails especially
        if opkkey == 'email':
            user[opkkey] = extract_email(user_row.get(nifkey)) or DEFAULT_EMAIL

        # We are nice about missing phonenumbers. This is relevant for logging
        # into booking. Use NIF PersonId instead.
        elif opkkey == 'userid':
            user[opkkey] = (gen_userid(user_row.get(nifkey))
                            or str(user_row[attribute_map['id']]))

        else:
            # We only want to assign key if there is a value for it.
            if user_row.get(nifkey):
                user[opkkey] = user_row.get(nifkey)

    return user


def gen_userid(string):
    """Normalize phonenumer to 8 integers

        Numbers are stripped of white space and
        special chars and then the rightmost 8
        integers are use as the phoneumber, eg:

        +47 92 88 44 92 => 92884492

        This should be what most people expect.
    """

    try:
        # Substitute non-integers with '' in string.
        ints = re.sub('\D', '', string)
    except TypeError:
        return None
    else:
        return ints[-8:]

def extract_email(email_data):
    """Sanitize email data

        Emails adresses from NIF are not sanitized. It may be
        one email, may be a list, may be a list with empty slots ....

        So we parse the list and extract the first valid email.

        check_mx=True is *way* to expensive to be of use.
    """

    if email_data is None:
        return None


    for address in email_data.split(';'):
        # If we find a valid email, just break the loop
        # and use it
        if validate_email(address, check_mx=False):
            return address

    return None

if __name__ == '__main__':

    for row in DATA:
        # Append user to list of users
        _ = gen_user_from_row(row)
        if _ is not None:
            MEMBERS.append(_)

    # Return JSON-serialized list of dict to be consumed by
    # wordpress plugin. Encoding utf-8 is important here.
    # NEVER NEVER NEVER EVER use latin1 or other encodings!!!!
    # No win1251, no latin15, no ISO-8859-1 or ISO-8859-15.
    # UTF8!
    print json.dumps(MEMBERS, ensure_ascii=False, encoding='utf8')

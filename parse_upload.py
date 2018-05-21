#!/usr/bin/python2.7
#coding: utf-8
"""Receive a filename and return a json object

    The file must be a modern (xlsx) Excel spreadsheet.

    The script parses the first sheet and returns the
    data as a JSON-serialized list of dicts.
"""
import json
import sys

from validate_email import validate_email

from common import xl_read_as_dict
from constants import ATTR_MAP as attribute_map, DEFAULTS as defaults
from constants import DEFAULT_EMAIL

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

        Missing values are replaced with some semi-sane defaults, as
        they trigger errors in the downstream booking system otherwise.

        Sad, but true. The correct solution is to populate the NIF
        database with missing values.

        Ref. mysql@opk.no:/opk/opk_booking_user_import
    """

    # Login id is phonemobile, so if mobile isn't set then drop user.
    # This is the harsh reality. All valid user can go to
    # https://mi.nif.no/MyProfile
    # and add their mobile number!
    if user_row['Tlf. mobil'] is None:
        return None

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

        # Update user object with correct attribute name
        # Handle emails especialy
        if user_row.get(nifkey) is None:
            user[opkkey] = default_value(nifkey)
        elif opkkey == 'email':
            user[opkkey] = extract_email(user_row.get(nifkey))
        else:
            user[opkkey] = user_row.get(nifkey)

    return user


def extract_email(email_data):
    """Sanitize email data

        Emails adresses from NIF are not sanitized. It may be
        one email, may be a list, may be a list with empty slots ....

        So we parse the list and extract the first valid email.

        This requires net access as this actually checks that this
        email address can be delivered.

        If this entails to many errors, verify=True can be replaced
        with the less invasive check_mx=True
    """

    result = DEFAULT_EMAIL

    for address in email_data.split(';'):
        if validate_email(address):
            result = address
            break

    return result


def default_value(key):
    """A default value, when key is defined bus missing a valuei

        Returns None if key is not defined or known.
    """

    return defaults.get(key)


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

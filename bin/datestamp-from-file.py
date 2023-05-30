#!/usr/bin/env python3
import os
import sys
import time

if len(sys.argv) > 1:
    stat = os.stat(sys.argv[1])
    mtime = stat.st_mtime
    print(time.strftime('%Y.%m.%d',time.localtime(mtime)))
    

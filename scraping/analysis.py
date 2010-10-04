#!/usr/bin/python

import yaml
import MySQLdb
from csc import divisi2

def db_query(conn, str, args):
	cursor = conn.cursor()
	cursor.execute(str % args)
	ret = cursor.fetchall()
	cursor.close()
	return ret

f = open('../includes/config.yaml')
config = yaml.load(f)
f.close()

conn = MySQLdb.connect(config['dbhost'],config['dbuser'],config['dbpass'] or '',config['dbname'])

courses = db_query(conn, "SELECT * FROM comments WHERE parent='1'", ())
data = []
for c in courses:
	topics = db_query(conn, "SELECT * FROM comments WHERE parent='%d'", (c[0]))
	for t in topics:
		data.append( (1, c[0], t[1]) )
mat = divisi2.make_sparse(data)
mat = mat.normalize_rows()
mat_t = mat.T
mult = divisi2.matrixmultiply(mat, mat_t)
print mult

similarities = mult.named_entries()
for s in similarities:
	v,c1,c2 = s
	if c1 != c2:
		db_query(conn, "REPLACE INTO similarities (cid1,cid2,val) VALUE (%d,%d,%f)", (c1,c2,v));

conn.close()

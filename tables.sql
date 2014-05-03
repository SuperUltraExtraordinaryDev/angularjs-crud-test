CREATE TABLE ci_sessions (
  session_id varchar(40) NOT NULL DEFAULT '0',
  ip_address varchar(16) NOT NULL DEFAULT '0',
  user_agent varchar(120) NOT NULL,
  last_activity int(10) unsigned NOT NULL DEFAULT '0',
  user_data text NOT NULL,
  PRIMARY KEY (session_id),
  KEY last_activity_idx (last_activity)
);

CREATE TABLE sys_users (
  userid int(11) NOT NULL AUTO_INCREMENT,
  username varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  authlevel int(1) DEFAULT NULL,
  firstname text,
  lastname text,
  email text,
  token text,
  PRIMARY KEY (userid)
);

CREATE TABLE tasks (
  taskId int(11) NOT NULL AUTO_INCREMENT,
  task varchar(200) DEFAULT NULL,
  `status` int(11) DEFAULT NULL,
  created_by text,
  created_at text,
  assigned_to text,
  due_date text,
  PRIMARY KEY (taskId)
);

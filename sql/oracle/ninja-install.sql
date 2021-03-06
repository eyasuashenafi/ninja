CREATE SEQUENCE  ninja_widgets_id_SEQ
  MINVALUE 1 MAXVALUE 999999999999999999999999 INCREMENT BY 1  NOCYCLE ;

CREATE SEQUENCE  recurring_downtime_id_SEQ
  MINVALUE 1 MAXVALUE 999999999999999999999999 INCREMENT BY 1  NOCYCLE ;

CREATE SEQUENCE  scheduled_report_types_id_SEQ
  MINVALUE 1 MAXVALUE 999999999999999999999999 INCREMENT BY 1  NOCYCLE ;

CREATE SEQUENCE  avail_config_objects_id_SEQ
  MINVALUE 1 MAXVALUE 999999999999999999999999 INCREMENT BY 1  NOCYCLE ;

CREATE SEQUENCE  roles_id_SEQ
  MINVALUE 1 MAXVALUE 999999999999999999999999 INCREMENT BY 1  NOCYCLE ;

CREATE SEQUENCE  ninja_user_authorization_id_SE
  MINVALUE 1 MAXVALUE 999999999999999999999999 INCREMENT BY 1  NOCYCLE ;

CREATE SEQUENCE  scheduled_report_periods_id_SE
  MINVALUE 1 MAXVALUE 999999999999999999999999 INCREMENT BY 1  NOCYCLE ;

CREATE SEQUENCE  users_id_SEQ
  MINVALUE 1 MAXVALUE 999999999999999999999999 INCREMENT BY 1  NOCYCLE ;

CREATE SEQUENCE  sla_periods_id_SEQ
  MINVALUE 1 MAXVALUE 999999999999999999999999 INCREMENT BY 1  NOCYCLE ;

CREATE SEQUENCE  sla_config_id_SEQ
  MINVALUE 1 MAXVALUE 999999999999999999999999 INCREMENT BY 1  NOCYCLE ;

CREATE SEQUENCE  ninja_settings_id_SEQ
  MINVALUE 1 MAXVALUE 999999999999999999999999 INCREMENT BY 1  NOCYCLE ;

CREATE SEQUENCE  scheduled_reports_id_SEQ
  MINVALUE 1 MAXVALUE 999999999999999999999999 INCREMENT BY 1  NOCYCLE ;

CREATE SEQUENCE  sla_config_objects_id_SEQ
  MINVALUE 1 MAXVALUE 999999999999999999999999 INCREMENT BY 1  NOCYCLE ;

CREATE SEQUENCE  user_tokens_id_SEQ
  MINVALUE 1 MAXVALUE 999999999999999999999999 INCREMENT BY 1  NOCYCLE ;

CREATE SEQUENCE  avail_config_id_SEQ
  MINVALUE 1 MAXVALUE 999999999999999999999999 INCREMENT BY 1  NOCYCLE ;

CREATE SEQUENCE  ninja_db_version_id_SEQ
  MINVALUE 1 MAXVALUE 999999999999999999999999 INCREMENT BY 1  NOCYCLE ;

CREATE SEQUENCE  summary_config_id_SEQ
  MINVALUE 1 MAXVALUE 999999999999999999999999 INCREMENT BY 1  NOCYCLE ;

CREATE SEQUENCE saved_searches_id_SEQ
  MINVALUE 1 MAXVALUE 999999999999999999999999 INCREMENT BY 1  NOCYCLE ;

CREATE TABLE ninja_saved_searches (
	id NUMBER(10,0) NOT NULL,
	username VARCHAR2(255 CHAR) DEFAULT NULL,
	search_name VARCHAR2(255 CHAR) NOT NULL,
	search_query VARCHAR2(255 CHAR) NOT NULL,
	search_description VARCHAR2(255 CHAR) NOT NULL
);
ALTER TABLE ninja_saved_searches ADD CONSTRAINT ninja_saved_searches_pk PRIMARY KEY(id) ENABLE;
CREATE INDEX n_s_s_username ON ninja_saved_searches(username);

CREATE TABLE avail_config (
  id NUMBER(10,0) NOT NULL,
  username VARCHAR2(255 CHAR) DEFAULT NULL,
  report_name VARCHAR2(255 CHAR) NOT NULL,
  info CLOB DEFAULT NULL,
  created DATE DEFAULT SYSDATE NOT NULL,
  rpttimeperiod VARCHAR2(75 CHAR) DEFAULT NULL,
  report_period VARCHAR2(50 CHAR) DEFAULT NULL,
  start_time NUMBER(10,0) DEFAULT '0' NOT NULL,
  end_time NUMBER(10,0) DEFAULT '0' NOT NULL,
  report_type VARCHAR2(30 CHAR) NOT NULL,
  initialassumedhoststate NUMBER(10,0) DEFAULT '0',
  initialassumedservicestate NUMBER(10,0) DEFAULT '0',
  assumeinitialstates NUMBER(10,0) DEFAULT '0' NOT NULL,
  scheduleddowntimeasuptime NUMBER(10,0) DEFAULT '0',
  assumestatesduringnotrunning NUMBER(10,0) DEFAULT '0',
  includesoftstates NUMBER(10,0) DEFAULT '0',
  updated DATE DEFAULT to_date('01-JAN-70 00:00:00', 'dd-MON-yy hh24:mi:ss') NOT NULL,
  use_average NUMBER(3,0) DEFAULT '0',
  use_alias NUMBER(3,0) DEFAULT '0',
  cluster_mode NUMBER(10,0) DEFAULT '0',
  host_filter_status VARCHAR(100 CHAR) DEFAULT NULL,
  service_filter_status VARCHAR(100 CHAR) DEFAULT NULL,
  use_summary NUMBER(3,0) DEFAULT '0',
  use_pnp NUMBER(3,0) DEFAULT '0',
  summary_report_type NUMBER(3,0) DEFAULT '0',
  summary_items NUMBER(3,0) DEFAULT '0',
  alert_types NUMBER(3,0) DEFAULT '0',
  state_types NUMBER(3,0) DEFAULT '0',
  host_states NUMBER(3,0) DEFAULT '0',
  service_states NUMBER(3,0) DEFAULT '0'
);
ALTER TABLE avail_config ADD CONSTRAINT avail_config_pk PRIMARY KEY(id) ENABLE;
CREATE INDEX user_1 ON avail_config(username);

CREATE TABLE avail_config_objects (
  id NUMBER(10,0) NOT NULL,
  avail_id NUMBER(10,0) DEFAULT '0' NOT NULL,
  name VARCHAR2(255 CHAR) NOT NULL
);
ALTER TABLE avail_config_objects ADD CONSTRAINT avail_config_objects_pk PRIMARY KEY(id) ENABLE;
CREATE INDEX avail_id ON avail_config_objects(avail_id);

CREATE TABLE avail_db_version (
  version NUMBER(10,0) DEFAULT '0' NOT NULL
);
INSERT INTO avail_db_version VALUES(7);
commit;

CREATE TABLE summary_config (
  id NUMBER(11,0) NOT NULL,
  username varchar2(200 CHAR) NOT NULL,
  report_name varchar2(200 CHAR) NOT NULL,
  setting CLOB NOT NULL
);
ALTER TABLE summary_config ADD CONSTRAINT summary_config_pk PRIMARY KEY(id) ENABLE;
CREATE INDEX username_sum_conf ON summary_config(username);

PROMPT Creating Table ninja_db_version ...
CREATE TABLE ninja_db_version (
  id NUMBER(10,0) NOT NULL,
  version NUMBER(10,0) DEFAULT '0' NOT NULL
);
ALTER TABLE ninja_db_version ADD CONSTRAINT ninja_db_version_pk PRIMARY KEY(id) ENABLE;

INSERT INTO ninja_db_version (id, version) VALUES(1, 1);
commit;

CREATE TABLE ninja_settings (
  id NUMBER(10,0) NOT NULL,
  username VARCHAR2(200 CHAR) DEFAULT NULL,
  page VARCHAR2(200 CHAR) NOT NULL,
  type VARCHAR2(200 CHAR) NOT NULL,
  setting CLOB DEFAULT NULL,
  widget_id NUMBER(10,0)
);
ALTER TABLE ninja_settings ADD CONSTRAINT ninja_settings_pk PRIMARY KEY(id) ENABLE;
CREATE INDEX user_3 ON ninja_settings(username);
CREATE INDEX page ON ninja_settings(page);
CREATE INDEX widget_id ON ninja_settings(widget_id);

INSERT INTO ninja_settings (id,username,page, type, setting)
 VALUES(1, '', 'tac/index', 'widget_order', 'widget-placeholder=widget-netw_outages,widget-tac_scheduled,widget-monitoring_performance|widget-placeholder1=widget-tac_disabled,widget-tac_acknowledged|widget-placeholder2=widget-netw_health,widget-geomap|widget-placeholder3=widget-tac_hosts,widget-tac_services,widget-tac_monfeat,widget-tac_problems');
commit;

CREATE TABLE ninja_user_authorization (
  id NUMBER(10,0) NOT NULL,
  user_id NUMBER(10,0) NOT NULL,
  system_information NUMBER(10,0) DEFAULT '0' NOT NULL,
  configuration_information NUMBER(10,0) DEFAULT '0' NOT NULL,
  system_commands NUMBER(10,0) DEFAULT '0' NOT NULL,
  all_services NUMBER(10,0) DEFAULT '0' NOT NULL,
  all_hosts NUMBER(10,0) DEFAULT '0' NOT NULL,
  all_service_commands NUMBER(10,0) DEFAULT '0' NOT NULL,
  all_host_commands NUMBER(10,0) DEFAULT '0' NOT NULL
);
ALTER TABLE ninja_user_authorization ADD CONSTRAINT ninja_user_authorization_pk PRIMARY KEY(id) ENABLE;
CREATE INDEX user_id ON ninja_user_authorization(user_id);

CREATE TABLE ninja_widgets (
  id NUMBER(10,0) NOT NULL,
  username VARCHAR2(200 CHAR) DEFAULT NULL,
  page VARCHAR2(200 CHAR) DEFAULT 'tac/index' NOT NULL,
  name VARCHAR2(255 CHAR) NOT NULL,
  friendly_name VARCHAR2(255 CHAR) NOT NULL,
  setting CLOB DEFAULT NULL
);
ALTER TABLE ninja_widgets ADD CONSTRAINT ninja_widgets_pk PRIMARY KEY(id) ENABLE;
CREATE INDEX username ON ninja_widgets(username);

INSERT INTO ninja_widgets (id, username, page, name, friendly_name, setting) VALUES 	(1, '', 'tac/index', 'tac_problems', 'Unhandled problems', '');
INSERT INTO ninja_widgets (id, username, page, name, friendly_name, setting) VALUES 	(2, '', 'tac/index', 'netw_health', 'Network health', '');
INSERT INTO ninja_widgets (id, username, page, name, friendly_name, setting) VALUES 	(3, '', 'tac/index', 'tac_scheduled', 'Scheduled downtime', '');
INSERT INTO ninja_widgets (id, username, page, name, friendly_name, setting) VALUES 	(4, '', 'tac/index', 'tac_acknowledged', 'Acknowledged problems', '');
INSERT INTO ninja_widgets (id, username, page, name, friendly_name, setting) VALUES 	(5, '', 'tac/index', 'tac_disabled', 'Disabled checks', '');
INSERT INTO ninja_widgets (id, username, page, name, friendly_name, setting) VALUES 	(6, '', 'tac/index', 'netw_outages', 'Network outages', '');
INSERT INTO ninja_widgets (id, username, page, name, friendly_name, setting) VALUES 	(7, '', 'tac/index', 'tac_hosts', 'Hosts', '');
INSERT INTO ninja_widgets (id, username, page, name, friendly_name, setting) VALUES 	(8, '', 'tac/index', 'tac_services', 'Services', '');
INSERT INTO ninja_widgets (id, username, page, name, friendly_name, setting) VALUES 	(9, '', 'tac/index', 'tac_monfeat', 'Monitoring features', '');
INSERT INTO ninja_widgets (id, username, page, name, friendly_name, setting) VALUES 	(10, '', 'status', 'status_totals', 'Status Totals', '');
INSERT INTO ninja_widgets (id, username, page, name, friendly_name, setting) VALUES 	(11, '', 'tac/index', 'monitoring_performance', 'Monitoring Performance', '');
INSERT INTO ninja_widgets (id, username, page, name, friendly_name, setting) VALUES 	(12, '', 'tac/index', 'geomap', 'Geomap', '');
commit;

CREATE TABLE recurring_downtime (
  id NUMBER(10,0) NOT NULL,
  author VARCHAR2(255 CHAR) NOT NULL,
  downtime_type VARCHAR2(255 CHAR) NOT NULL,
  data CLOB NOT NULL,
  last_update NUMBER(10,0) DEFAULT '0' NOT NULL
);
ALTER TABLE recurring_downtime ADD CONSTRAINT recurring_downtime_pk PRIMARY KEY(id) ENABLE;
CREATE INDEX author ON recurring_downtime(author);
CREATE INDEX downtime_type ON recurring_downtime(downtime_type);

CREATE TABLE roles (
  id NUMBER(10,0) NOT NULL,
  name VARCHAR2(100 CHAR) NOT NULL,
  description VARCHAR2(255 CHAR) NOT NULL
);
ALTER TABLE roles ADD CONSTRAINT roles_pk PRIMARY KEY(id) ENABLE;
CREATE UNIQUE INDEX uniq_name ON roles(name);

INSERT INTO roles (id, name, description) VALUES (1, 'login', 'Login privileges, granted after account confirmation');
INSERT INTO roles (id, name, description) VALUES (2, 'admin', 'Administrative user, has access to everything.');
commit;

CREATE TABLE roles_users (
  user_id NUMBER(10,0) NOT NULL,
  role_id NUMBER(10,0) NOT NULL
);
ALTER TABLE roles_users ADD CONSTRAINT roles_users_pk PRIMARY KEY(user_id, role_id) ENABLE;
CREATE INDEX fk_role_id ON roles_users(role_id);

CREATE TABLE scheduled_report_periods (
  id NUMBER(10,0) NOT NULL,
  periodname VARCHAR2(100 CHAR) NOT NULL
);
ALTER TABLE scheduled_report_periods ADD CONSTRAINT scheduled_report_periods_pk PRIMARY KEY(id) ENABLE;

INSERT INTO scheduled_report_periods (id, periodname) VALUES (1, 'Weekly');
INSERT INTO scheduled_report_periods (id, periodname) VALUES (2, 'Monthly');
INSERT INTO scheduled_report_periods (id, periodname) VALUES (3, 'Daily');
commit;

CREATE TABLE scheduled_report_types (
  id NUMBER(10,0) NOT NULL,
  name VARCHAR2(255 CHAR) NOT NULL,
  script_reports_path VARCHAR2(255 CHAR) NOT NULL,
  script_reports_run VARCHAR2(255 CHAR) NOT NULL,
  identifier VARCHAR2(50 CHAR) NOT NULL
);
ALTER TABLE scheduled_report_types ADD CONSTRAINT scheduled_report_types_pk PRIMARY KEY(id) ENABLE;
CREATE INDEX identifier ON scheduled_report_types(identifier);

INSERT INTO scheduled_report_types (id, name, script_reports_path, script_reports_run, identifier) VALUES (1, ' ', ' ', ' ', 'avail');
INSERT INTO scheduled_report_types (id, name, script_reports_path, script_reports_run, identifier) VALUES (2, ' ', ' ', ' ', 'sla');
INSERT INTO scheduled_report_types (id, name, script_reports_path, script_reports_run, identifier) VALUES (3, ' ', ' ', ' ', 'summary');
commit;

CREATE TABLE scheduled_reports (
  id NUMBER(10,0) NOT NULL,
  username VARCHAR2(255 CHAR) DEFAULT NULL,
  report_type_id NUMBER(10,0) DEFAULT '0' NOT NULL,
  report_id NUMBER(10,0) DEFAULT '0' NOT NULL,
  recipients CLOB NOT NULL,
  description CLOB NOT NULL,
  period_id NUMBER(10,0) DEFAULT '0' NOT NULL,
  filename VARCHAR2(255 CHAR) NOT NULL
);
ALTER TABLE scheduled_reports ADD CONSTRAINT scheduled_reports_pk PRIMARY KEY(id) ENABLE;
CREATE INDEX report_type_id ON scheduled_reports(report_type_id);
CREATE INDEX user_2 ON scheduled_reports(username);

CREATE TABLE scheduled_reports_db_version (
  version VARCHAR2(10 CHAR) NOT NULL
);
INSERT INTO scheduled_reports_db_version VALUES('1.0.0');
commit;

CREATE TABLE sla_config (
  id NUMBER(10,0) NOT NULL,
  username VARCHAR2(255 CHAR) DEFAULT NULL,
  sla_name VARCHAR2(255 CHAR) NOT NULL,
  info CLOB DEFAULT NULL,
  created DATE DEFAULT SYSDATE NOT NULL,
  rpttimeperiod VARCHAR2(75 CHAR) DEFAULT NULL,
  report_period VARCHAR2(50 CHAR) DEFAULT NULL,
  start_time NUMBER(10,0) DEFAULT '0' NOT NULL,
  end_time NUMBER(10,0) DEFAULT '0' NOT NULL,
  report_type VARCHAR2(30 CHAR) NOT NULL,
  initialassumedhoststate NUMBER(10,0) DEFAULT '0' NOT NULL,
  initialassumedservicestate NUMBER(10,0) DEFAULT '0' NOT NULL,
  assumeinitialstates NUMBER(10,0) DEFAULT '0' NOT NULL,
  scheduleddowntimeasuptime NUMBER(10,0) DEFAULT '0',
  assumestatesduringnotrunning NUMBER(10,0) DEFAULT '0',
  includesoftstates NUMBER(10,0) DEFAULT '0',
  updated DATE DEFAULT to_date('01-JAN-70 00:00:00', 'dd-MON-yy hh24:mi:ss') NOT NULL,
  use_average NUMBER(3,0) DEFAULT '0',
  use_alias NUMBER(3,0) DEFAULT '0',
  cluster_mode NUMBER(10,0) DEFAULT '0',
  use_summary NUMBER(3,0) DEFAULT '0',
  use_pnp NUMBER(3,0) DEFAULT '0',
  summary_report_type NUMBER(3,0) DEFAULT '0',
  summary_items NUMBER(3,0) DEFAULT '0',
  alert_types NUMBER(3,0) DEFAULT '0',
  state_types NUMBER(3,0) DEFAULT '0',
  host_states NUMBER(3,0) DEFAULT '0',
  service_states NUMBER(3,0) DEFAULT '0'
);
ALTER TABLE sla_config ADD CONSTRAINT sla_config_pk PRIMARY KEY(id) ENABLE;

CREATE TABLE sla_config_objects (
  id NUMBER(10,0) NOT NULL,
  sla_id NUMBER(10,0) DEFAULT '0' NOT NULL,
  name VARCHAR2(255 CHAR) NOT NULL
);
ALTER TABLE sla_config_objects ADD CONSTRAINT sla_config_objects_pk PRIMARY KEY(id)ENABLE;
CREATE INDEX sla_id ON sla_config_objects(sla_id);

CREATE TABLE sla_db_version (
  version NUMBER(10,0) DEFAULT '0' NOT NULL
);
INSERT INTO sla_db_version VALUES(7);
commit;

CREATE TABLE sla_periods (
  id NUMBER(10,0) NOT NULL,
  sla_id NUMBER(10,0) DEFAULT '0' NOT NULL,
  name VARCHAR2(20 CHAR) NOT NULL,
  value FLOAT DEFAULT '0' NOT NULL
);
ALTER TABLE sla_periods ADD CONSTRAINT sla_periods_pk PRIMARY KEY(id) ENABLE;
CREATE INDEX sla_id_1 ON sla_periods(sla_id);

CREATE TABLE user_tokens (
  id NUMBER(10,0) NOT NULL,
  user_id NUMBER(10,0) NOT NULL,
  user_agent VARCHAR2(40 CHAR) NOT NULL,
  token VARCHAR2(32 CHAR) NOT NULL,
  created NUMBER(10,0) NOT NULL,
  expires NUMBER(10,0) NOT NULL
);


ALTER TABLE user_tokens ADD CONSTRAINT user_tokens_pk PRIMARY KEY(id) ENABLE;
CREATE UNIQUE INDEX uniq_token ON user_tokens(token);
CREATE INDEX fk_user_id ON user_tokens(user_id);

CREATE TABLE users (
  id NUMBER(10,0) NOT NULL,
  realname VARCHAR2(100 CHAR) DEFAULT NULL, -- originally: NOT NULL
  email VARCHAR2(127 CHAR) DEFAULT NULL,
  username VARCHAR2(100 CHAR) NOT NULL,
  password_algo VARCHAR2(20 CHAR) DEFAULT 'b64_sha1' NOT NULL,
  password VARCHAR2(50 CHAR) NOT NULL,
  logins NUMBER(10,0) DEFAULT '0' NOT NULL,
  last_login NUMBER(10,0)
);
ALTER TABLE users ADD CONSTRAINT users_pk PRIMARY KEY(id) ENABLE;
CREATE UNIQUE INDEX uniq_username ON users(username);


CREATE OR REPLACE TRIGGER ninja_widgets_id_TRG BEFORE INSERT OR UPDATE ON ninja_widgets
FOR EACH ROW
DECLARE
v_newVal NUMBER(12) := 0;
v_incval NUMBER(12) := 0;
BEGIN
  IF INSERTING AND :new.id IS NULL THEN
    SELECT  ninja_widgets_id_SEQ.NEXTVAL INTO v_newVal FROM DUAL;
    -- If this is the first time this table have been inserted into (sequence == 1)
    IF v_newVal = 1 THEN
      --get the max indentity value from the table
      SELECT NVL(max(id),0) INTO v_newVal FROM ninja_widgets;
      v_newVal := v_newVal + 1;
      --set the sequence to that value
      LOOP
           EXIT WHEN v_incval>=v_newVal;
           SELECT ninja_widgets_id_SEQ.nextval INTO v_incval FROM dual;
      END LOOP;
    END IF;
    --used to emulate LAST_INSERT_ID()
    --mysql_utilities.identity := v_newVal;
   -- assign the value from the sequence to emulate the identity column
   :new.id := v_newVal;
  END IF;
END;

/

CREATE OR REPLACE TRIGGER recurring_downtime_id_TRG BEFORE INSERT OR UPDATE ON recurring_downtime
FOR EACH ROW
DECLARE
v_newVal NUMBER(12) := 0;
v_incval NUMBER(12) := 0;
BEGIN
  IF INSERTING AND :new.id IS NULL THEN
    SELECT  recurring_downtime_id_SEQ.NEXTVAL INTO v_newVal FROM DUAL;
    -- If this is the first time this table have been inserted into (sequence == 1)
    IF v_newVal = 1 THEN
      --get the max indentity value from the table
      SELECT NVL(max(id),0) INTO v_newVal FROM recurring_downtime;
      v_newVal := v_newVal + 1;
      --set the sequence to that value
      LOOP
           EXIT WHEN v_incval>=v_newVal;
           SELECT recurring_downtime_id_SEQ.nextval INTO v_incval FROM dual;
      END LOOP;
    END IF;
    --used to emulate LAST_INSERT_ID()
    --mysql_utilities.identity := v_newVal;
   -- assign the value from the sequence to emulate the identity column
   :new.id := v_newVal;
  END IF;
END;

/

CREATE OR REPLACE TRIGGER scheduled_report_types_id_TRG BEFORE INSERT OR UPDATE ON scheduled_report_types
FOR EACH ROW
DECLARE
v_newVal NUMBER(12) := 0;
v_incval NUMBER(12) := 0;
BEGIN
  IF INSERTING AND :new.id IS NULL THEN
    SELECT  scheduled_report_types_id_SEQ.NEXTVAL INTO v_newVal FROM DUAL;
    -- If this is the first time this table have been inserted into (sequence == 1)
    IF v_newVal = 1 THEN
      --get the max indentity value from the table
      SELECT NVL(max(id),0) INTO v_newVal FROM scheduled_report_types;
      v_newVal := v_newVal + 1;
      --set the sequence to that value
      LOOP
           EXIT WHEN v_incval>=v_newVal;
           SELECT scheduled_report_types_id_SEQ.nextval INTO v_incval FROM dual;
      END LOOP;
    END IF;
    --used to emulate LAST_INSERT_ID()
    --mysql_utilities.identity := v_newVal;
   -- assign the value from the sequence to emulate the identity column
   :new.id := v_newVal;
  END IF;
END;

/

CREATE OR REPLACE TRIGGER avail_config_objects_id_TRG BEFORE INSERT OR UPDATE ON avail_config_objects
FOR EACH ROW
DECLARE
v_newVal NUMBER(12) := 0;
v_incval NUMBER(12) := 0;
BEGIN
  IF INSERTING AND :new.id IS NULL THEN
    SELECT  avail_config_objects_id_SEQ.NEXTVAL INTO v_newVal FROM DUAL;
    -- If this is the first time this table have been inserted into (sequence == 1)
    IF v_newVal = 1 THEN
      --get the max indentity value from the table
      SELECT NVL(max(id),0) INTO v_newVal FROM avail_config_objects;
      v_newVal := v_newVal + 1;
      --set the sequence to that value
      LOOP
           EXIT WHEN v_incval>=v_newVal;
           SELECT avail_config_objects_id_SEQ.nextval INTO v_incval FROM dual;
      END LOOP;
    END IF;
    --used to emulate LAST_INSERT_ID()
    --mysql_utilities.identity := v_newVal;
   -- assign the value from the sequence to emulate the identity column
   :new.id := v_newVal;
  END IF;
END;

/

CREATE OR REPLACE TRIGGER roles_id_TRG BEFORE INSERT OR UPDATE ON roles
FOR EACH ROW
DECLARE
v_newVal NUMBER(12) := 0;
v_incval NUMBER(12) := 0;
BEGIN
  IF INSERTING AND :new.id IS NULL THEN
    SELECT  roles_id_SEQ.NEXTVAL INTO v_newVal FROM DUAL;
    -- If this is the first time this table have been inserted into (sequence == 1)
    IF v_newVal = 1 THEN
      --get the max indentity value from the table
      SELECT NVL(max(id),0) INTO v_newVal FROM roles;
      v_newVal := v_newVal + 1;
      --set the sequence to that value
      LOOP
           EXIT WHEN v_incval>=v_newVal;
           SELECT roles_id_SEQ.nextval INTO v_incval FROM dual;
      END LOOP;
    END IF;
    --used to emulate LAST_INSERT_ID()
    --mysql_utilities.identity := v_newVal;
   -- assign the value from the sequence to emulate the identity column
   :new.id := v_newVal;
  END IF;
END;

/

CREATE OR REPLACE TRIGGER ninja_user_authorization_id_TR BEFORE INSERT OR UPDATE ON ninja_user_authorization
FOR EACH ROW
DECLARE
v_newVal NUMBER(12) := 0;
v_incval NUMBER(12) := 0;
BEGIN
  IF INSERTING AND :new.id IS NULL THEN
    SELECT  ninja_user_authorization_id_SE.NEXTVAL INTO v_newVal FROM DUAL;
    -- If this is the first time this table have been inserted into (sequence == 1)
    IF v_newVal = 1 THEN
      --get the max indentity value from the table
      SELECT NVL(max(id),0) INTO v_newVal FROM ninja_user_authorization;
      v_newVal := v_newVal + 1;
      --set the sequence to that value
      LOOP
           EXIT WHEN v_incval>=v_newVal;
           SELECT ninja_user_authorization_id_SE.nextval INTO v_incval FROM dual;
      END LOOP;
    END IF;
    --used to emulate LAST_INSERT_ID()
    --mysql_utilities.identity := v_newVal;
   -- assign the value from the sequence to emulate the identity column
   :new.id := v_newVal;
  END IF;
END;

/

CREATE OR REPLACE TRIGGER scheduled_report_periods_id_TR BEFORE INSERT OR UPDATE ON scheduled_report_periods
FOR EACH ROW
DECLARE
v_newVal NUMBER(12) := 0;
v_incval NUMBER(12) := 0;
BEGIN
  IF INSERTING AND :new.id IS NULL THEN
    SELECT  scheduled_report_periods_id_SE.NEXTVAL INTO v_newVal FROM DUAL;
    -- If this is the first time this table have been inserted into (sequence == 1)
    IF v_newVal = 1 THEN
      --get the max indentity value from the table
      SELECT NVL(max(id),0) INTO v_newVal FROM scheduled_report_periods;
      v_newVal := v_newVal + 1;
      --set the sequence to that value
      LOOP
           EXIT WHEN v_incval>=v_newVal;
           SELECT scheduled_report_periods_id_SE.nextval INTO v_incval FROM dual;
      END LOOP;
    END IF;
    --used to emulate LAST_INSERT_ID()
    --mysql_utilities.identity := v_newVal;
   -- assign the value from the sequence to emulate the identity column
   :new.id := v_newVal;
  END IF;
END;

/

CREATE OR REPLACE TRIGGER users_id_TRG BEFORE INSERT OR UPDATE ON users
FOR EACH ROW
DECLARE
v_newVal NUMBER(12) := 0;
v_incval NUMBER(12) := 0;
BEGIN
  IF INSERTING AND :new.id IS NULL THEN
    SELECT  users_id_SEQ.NEXTVAL INTO v_newVal FROM DUAL;
    -- If this is the first time this table have been inserted into (sequence == 1)
    IF v_newVal = 1 THEN
      --get the max indentity value from the table
      SELECT NVL(max(id),0) INTO v_newVal FROM users;
      v_newVal := v_newVal + 1;
      --set the sequence to that value
      LOOP
           EXIT WHEN v_incval>=v_newVal;
           SELECT users_id_SEQ.nextval INTO v_incval FROM dual;
      END LOOP;
    END IF;
    --used to emulate LAST_INSERT_ID()
    --mysql_utilities.identity := v_newVal;
   -- assign the value from the sequence to emulate the identity column
   :new.id := v_newVal;
  END IF;
END;

/

CREATE OR REPLACE TRIGGER sla_periods_id_TRG BEFORE INSERT OR UPDATE ON sla_periods
FOR EACH ROW
DECLARE
v_newVal NUMBER(12) := 0;
v_incval NUMBER(12) := 0;
BEGIN
  IF INSERTING AND :new.id IS NULL THEN
    SELECT  sla_periods_id_SEQ.NEXTVAL INTO v_newVal FROM DUAL;
    -- If this is the first time this table have been inserted into (sequence == 1)
    IF v_newVal = 1 THEN
      --get the max indentity value from the table
      SELECT NVL(max(id),0) INTO v_newVal FROM sla_periods;
      v_newVal := v_newVal + 1;
      --set the sequence to that value
      LOOP
           EXIT WHEN v_incval>=v_newVal;
           SELECT sla_periods_id_SEQ.nextval INTO v_incval FROM dual;
      END LOOP;
    END IF;
    --used to emulate LAST_INSERT_ID()
    --mysql_utilities.identity := v_newVal;
   -- assign the value from the sequence to emulate the identity column
   :new.id := v_newVal;
  END IF;
END;

/

CREATE OR REPLACE TRIGGER sla_config_id_TRG BEFORE INSERT OR UPDATE ON sla_config
FOR EACH ROW
DECLARE
v_newVal NUMBER(12) := 0;
v_incval NUMBER(12) := 0;
BEGIN
  IF INSERTING AND :new.id IS NULL THEN
    SELECT  sla_config_id_SEQ.NEXTVAL INTO v_newVal FROM DUAL;
    -- If this is the first time this table have been inserted into (sequence == 1)
    IF v_newVal = 1 THEN
      --get the max indentity value from the table
      SELECT NVL(max(id),0) INTO v_newVal FROM sla_config;
      v_newVal := v_newVal + 1;
      --set the sequence to that value
      LOOP
           EXIT WHEN v_incval>=v_newVal;
           SELECT sla_config_id_SEQ.nextval INTO v_incval FROM dual;
      END LOOP;
    END IF;
    --used to emulate LAST_INSERT_ID()
    --mysql_utilities.identity := v_newVal;
   -- assign the value from the sequence to emulate the identity column
   :new.id := v_newVal;
  END IF;
END;

/

CREATE OR REPLACE TRIGGER ninja_settings_id_TRG BEFORE INSERT OR UPDATE ON ninja_settings
FOR EACH ROW
DECLARE
v_newVal NUMBER(12) := 0;
v_incval NUMBER(12) := 0;
BEGIN
  IF INSERTING AND :new.id IS NULL THEN
    SELECT  ninja_settings_id_SEQ.NEXTVAL INTO v_newVal FROM DUAL;
    -- If this is the first time this table have been inserted into (sequence == 1)
    IF v_newVal = 1 THEN
      --get the max indentity value from the table
      SELECT NVL(max(id),0) INTO v_newVal FROM ninja_settings;
      v_newVal := v_newVal + 1;
      --set the sequence to that value
      LOOP
           EXIT WHEN v_incval>=v_newVal;
           SELECT ninja_settings_id_SEQ.nextval INTO v_incval FROM dual;
      END LOOP;
    END IF;
    --used to emulate LAST_INSERT_ID()
    --mysql_utilities.identity := v_newVal;
   -- assign the value from the sequence to emulate the identity column
   :new.id := v_newVal;
  END IF;
END;

/

CREATE OR REPLACE TRIGGER scheduled_reports_id_TRG BEFORE INSERT OR UPDATE ON scheduled_reports
FOR EACH ROW
DECLARE
v_newVal NUMBER(12) := 0;
v_incval NUMBER(12) := 0;
BEGIN
  IF INSERTING AND :new.id IS NULL THEN
    SELECT  scheduled_reports_id_SEQ.NEXTVAL INTO v_newVal FROM DUAL;
    -- If this is the first time this table have been inserted into (sequence == 1)
    IF v_newVal = 1 THEN
      --get the max indentity value from the table
      SELECT NVL(max(id),0) INTO v_newVal FROM scheduled_reports;
      v_newVal := v_newVal + 1;
      --set the sequence to that value
      LOOP
           EXIT WHEN v_incval>=v_newVal;
           SELECT scheduled_reports_id_SEQ.nextval INTO v_incval FROM dual;
      END LOOP;
    END IF;
    --used to emulate LAST_INSERT_ID()
    --mysql_utilities.identity := v_newVal;
   -- assign the value from the sequence to emulate the identity column
   :new.id := v_newVal;
  END IF;
END;

/

CREATE OR REPLACE TRIGGER sla_config_objects_id_TRG BEFORE INSERT OR UPDATE ON sla_config_objects
FOR EACH ROW
DECLARE
v_newVal NUMBER(12) := 0;
v_incval NUMBER(12) := 0;
BEGIN
  IF INSERTING AND :new.id IS NULL THEN
    SELECT  sla_config_objects_id_SEQ.NEXTVAL INTO v_newVal FROM DUAL;
    -- If this is the first time this table have been inserted into (sequence == 1)
    IF v_newVal = 1 THEN
      --get the max indentity value from the table
      SELECT NVL(max(id),0) INTO v_newVal FROM sla_config_objects;
      v_newVal := v_newVal + 1;
      --set the sequence to that value
      LOOP
           EXIT WHEN v_incval>=v_newVal;
           SELECT sla_config_objects_id_SEQ.nextval INTO v_incval FROM dual;
      END LOOP;
    END IF;
    --used to emulate LAST_INSERT_ID()
    --mysql_utilities.identity := v_newVal;
   -- assign the value from the sequence to emulate the identity column
   :new.id := v_newVal;
  END IF;
END;

/

CREATE OR REPLACE TRIGGER user_tokens_id_TRG BEFORE INSERT OR UPDATE ON user_tokens
FOR EACH ROW
DECLARE
v_newVal NUMBER(12) := 0;
v_incval NUMBER(12) := 0;
BEGIN
  IF INSERTING AND :new.id IS NULL THEN
    SELECT  user_tokens_id_SEQ.NEXTVAL INTO v_newVal FROM DUAL;
    -- If this is the first time this table have been inserted into (sequence == 1)
    IF v_newVal = 1 THEN
      --get the max indentity value from the table
      SELECT NVL(max(id),0) INTO v_newVal FROM user_tokens;
      v_newVal := v_newVal + 1;
      --set the sequence to that value
      LOOP
           EXIT WHEN v_incval>=v_newVal;
           SELECT user_tokens_id_SEQ.nextval INTO v_incval FROM dual;
      END LOOP;
    END IF;
    --used to emulate LAST_INSERT_ID()
    --mysql_utilities.identity := v_newVal;
   -- assign the value from the sequence to emulate the identity column
   :new.id := v_newVal;
  END IF;
END;

/

CREATE OR REPLACE TRIGGER avail_config_id_TRG BEFORE INSERT OR UPDATE ON avail_config
FOR EACH ROW
DECLARE
v_newVal NUMBER(12) := 0;
v_incval NUMBER(12) := 0;
BEGIN
  IF INSERTING AND :new.id IS NULL THEN
    SELECT  avail_config_id_SEQ.NEXTVAL INTO v_newVal FROM DUAL;
    -- If this is the first time this table have been inserted into (sequence == 1)
    IF v_newVal = 1 THEN
      --get the max indentity value from the table
      SELECT NVL(max(id),0) INTO v_newVal FROM avail_config;
      v_newVal := v_newVal + 1;
      --set the sequence to that value
      LOOP
           EXIT WHEN v_incval>=v_newVal;
           SELECT avail_config_id_SEQ.nextval INTO v_incval FROM dual;
      END LOOP;
    END IF;
    --used to emulate LAST_INSERT_ID()
    --mysql_utilities.identity := v_newVal;
   -- assign the value from the sequence to emulate the identity column
   :new.id := v_newVal;
  END IF;
END;

/

CREATE OR REPLACE TRIGGER ninja_db_version_id_TRG BEFORE INSERT OR UPDATE ON ninja_db_version
FOR EACH ROW
DECLARE
v_newVal NUMBER(12) := 0;
v_incval NUMBER(12) := 0;
BEGIN
  IF INSERTING AND :new.id IS NULL THEN
    SELECT  ninja_db_version_id_SEQ.NEXTVAL INTO v_newVal FROM DUAL;
    -- If this is the first time this table have been inserted into (sequence == 1)
    IF v_newVal = 1 THEN
      --get the max indentity value from the table
      SELECT NVL(max(id),0) INTO v_newVal FROM ninja_db_version;
      v_newVal := v_newVal + 1;
      --set the sequence to that value
      LOOP
           EXIT WHEN v_incval>=v_newVal;
           SELECT ninja_db_version_id_SEQ.nextval INTO v_incval FROM dual;
      END LOOP;
    END IF;
    --used to emulate LAST_INSERT_ID()
    --mysql_utilities.identity := v_newVal;
   -- assign the value from the sequence to emulate the identity column
   :new.id := v_newVal;
  END IF;
END;

/

CREATE OR REPLACE TRIGGER summary_config_id_TRG BEFORE INSERT OR UPDATE ON summary_config
FOR EACH ROW
DECLARE
v_newVal NUMBER(12) := 0;
v_incval NUMBER(12) := 0;
BEGIN
  IF INSERTING AND :new.id IS NULL THEN
    SELECT  summary_config_id_SEQ.NEXTVAL INTO v_newVal FROM DUAL;
    -- If this is the first time this table have been inserted into (sequence == 1)
    IF v_newVal = 1 THEN
      --get the max indentity value from the table
      SELECT NVL(max(id),0) INTO v_newVal FROM summary_config;
      v_newVal := v_newVal + 1;
      --set the sequence to that value
      LOOP
           EXIT WHEN v_incval>=v_newVal;
           SELECT summary_config_id_SEQ.nextval INTO v_incval FROM dual;
      END LOOP;
    END IF;
    --used to emulate LAST_INSERT_ID()
    --mysql_utilities.identity := v_newVal;
   -- assign the value from the sequence to emulate the identity column
   :new.id := v_newVal;
  END IF;
END;

/

CREATE OR REPLACE TRIGGER saved_searches_id_TRG BEFORE INSERT OR UPDATE ON ninja_saved_searches
FOR EACH ROW
DECLARE
v_newVal NUMBER(12) := 0;
v_incval NUMBER(12) := 0;
BEGIN
  IF INSERTING AND :new.id IS NULL THEN
    SELECT saved_searches_id_SEQ.NEXTVAL INTO v_newVal FROM DUAL;
    -- If this is the first time this table have been inserted into (sequence == 1)
    IF v_newVal = 1 THEN
      --get the max indentity value from the table
      SELECT NVL(max(id),0) INTO v_newVal FROM ninja_saved_searches;
      v_newVal := v_newVal + 1;
      --set the sequence to that value
      LOOP
           EXIT WHEN v_incval>=v_newVal;
           SELECT saved_searches_id_SEQ.nextval INTO v_incval FROM dual;
      END LOOP;
    END IF;
    --used to emulate LAST_INSERT_ID()
    --mysql_utilities.identity := v_newVal;
   -- assign the value from the sequence to emulate the identity column
   :new.id := v_newVal;
  END IF;
END;

/

-- DISCONNECT;
SPOOL OFF;
-- exit;


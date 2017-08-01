#
# Table structure for table 'tx_news_domain_model_news'
#
CREATE TABLE tx_news_domain_model_news (
  tx_roqnewsevent_is_event tinyint(1) unsigned DEFAULT '0' NOT NULL,
  tx_roqnewsevent_start int(11) DEFAULT '0' NOT NULL,
  tx_roqnewsevent_end int(11) DEFAULT '0' NOT NULL,
  tx_roqnewsevent_location varchar(255) DEFAULT '' NOT NULL,
);

/*
 * Copyright (c) 2006-2007 Massachusetts General Hospital 
 * All rights reserved. This program and the accompanying materials 
 * are made available under the terms of the i2b2 Software License v1.0 
 * which accompanies this distribution. 
 * 
 * Contributors: 
 *     Rajesh Kuttan
 */
package edu.harvard.i2b2.crc.util;

import java.io.IOException;
import java.sql.Connection;
import java.sql.DriverManager;
import java.sql.SQLException;
import java.util.Properties;

import javax.sql.DataSource;

import org.apache.commons.logging.Log;
import org.apache.commons.logging.LogFactory;
import org.springframework.beans.factory.BeanFactory;
import org.springframework.beans.factory.config.PropertiesFactoryBean;
import org.springframework.context.support.ClassPathXmlApplicationContext;
import org.springframework.core.io.ClassPathResource;
import org.springframework.core.io.Resource;

import edu.harvard.i2b2.common.exception.I2B2Exception;
import edu.harvard.i2b2.common.util.ServiceLocator;
import edu.harvard.i2b2.common.util.ServiceLocatorException;
import edu.harvard.i2b2.crc.ejb.PdoQueryLocalHome;
import edu.harvard.i2b2.crc.ejb.QueryInfoLocalHome;
import edu.harvard.i2b2.crc.ejb.QueryManagerLocalHome;
import edu.harvard.i2b2.crc.ejb.QueryResultLocalHome;
import edu.harvard.i2b2.crc.ejb.QueryRunLocalHome;

/**
 * This is the CRC application's main utility class This utility class provides
 * support for fetching resources like datasouce, to read application
 * properties, to get ejb home,etc. $Id: QueryProcessorUtil.java,v 1.7
 * 2007/04/25 15:05:11 rk903 Exp $
 * 
 * @author rkuttan
 */
public class QueryProcessorUtil {

	/** log **/
	protected final static Log log = LogFactory
			.getLog(QueryProcessorUtil.class);

	/** property file name which holds application directory name **/
	public static final String APPLICATION_DIRECTORY_PROPERTIES_FILENAME = "crc_application_directory.properties";

	/** application directory property name **/
	public static final String APPLICATIONDIR_PROPERTIES = "edu.harvard.i2b2.crc.applicationdir";

	/** application property filename* */
	public static final String APPLICATION_PROPERTIES_FILENAME = "crc.properties";

	/** property name for query manager ejb present in app property file* */
	private static final String EJB_LOCAL_JNDI_QUERYMANAGER_PROPERTIES = "queryprocessor.jndi.querymanagerlocal";

	/** property name for query info ejb present in app property file* */
	private static final String EJB_LOCAL_JNDI_QUERYINFO_PROPERTIES = "queryprocessor.jndi.queryinfolocal";

	/** property name for query run ejb present in app property file* */
	private static final String EJB_LOCAL_JNDI_QUERYRUN_PROPERTIES = "queryprocessor.jndi.queryrunlocal";

	/** property name for query result ejb present in app property file* */
	private static final String EJB_LOCAL_JNDI_QUERYRESULT_PROPERTIES = "queryprocessor.jndi.queryresultlocal";

	/** property name for pdo query ejb present in app property file* */
	private static final String EJB_LOCAL_JNDI_PDOQUERY_PROPERTIES = "queryprocessor.jndi.pdoquerylocal";

	/** property name for datasource present in app property file* */
	private static final String DATASOURCE_JNDI_PROPERTIES = "queryprocessor.jndi.datasource_name";

	/** property name for database connection string in app property file* */
	private static final String DATABASE_CONNECTION_STRING_PROPERTIES = "queryprocessor.database.connection_string";
	/** property name for database user in app property file* */
	private static final String DATABASE_CONNECTION_USER_PROPERTIES = "queryprocessor.database.user";
	/** property name for database password in app property file* */
	private static final String DATABASE_CONNECTION_PASSWORD_PROPERTIES = "queryprocessor.database.password";

	/** property name for metadata schema name* */
	private static final String METADATA_SCHEMA_NAME_PROPERTIES = "queryprocessor.db.metadataschema";

	/** property name for metadata schema name* */
	private static final String PMCELL_WS_URL_PROPERTIES = "queryprocessor.ws.pm.url";

	/** property name for PM bypass flag **/
	private static final String PMCELL_BYPASS_FLAG_PROPERTIES = "queryprocessor.ws.pm.bypass";

	/** property name for PM bypass project role name* */
	private static final String PMCELL_BYPASS_ROLE_PROPERTIES = "queryprocessor.ws.pm.bypass.role";

	/** property name for pm bypass project name **/
	private static final String PMCELL_BYPASS_PROJECT_PROPERTIES = "queryprocessor.ws.pm.bypass.project";

	/** property name for metadata schema name* */
	private static final String DS_LOOKUP_DATASOURCE_PROPERTIES = "queryprocessor.ds.lookup.datasource";

	/** property name for metadata schema name* */
	private static final String DS_LOOKUP_SCHEMANAME_PROPERTIES = "queryprocessor.ds.lookup.schemaname";

	/** property name for metadata schema name* */
	private static final String DS_LOOKUP_SERVERTYPE_PROPERTIES = "queryprocessor.ds.lookup.servertype";

	/** property name for ontology url schema name **/
	private static final String ONTOLOGYCELL_WS_URL_PROPERTIES = "queryprocessor.ws.ontology.url";

	/** spring bean name for datasource **/
	private static final String DATASOURCE_BEAN_NAME = "dataSource";

	public static final String DEFAULT_SETFINDER_RESULT_BEANNAME = "defaultSetfinderResultType";

	/** class instance field* */
	private static QueryProcessorUtil thisInstance = null;

	/** service locator field* */
	private static ServiceLocator serviceLocator = null;

	/** field to store application properties * */
	private static Properties appProperties = null;

	private static Properties loadProperties = null;

	/** field to store app datasource* */
	private DataSource dataSource = null;

	/** single instance of spring bean factory* */
	private BeanFactory beanFactory = null;

	/**
	 * Private constructor to make the class singleton
	 */
	private QueryProcessorUtil() {
	}

	static {
		try {
			Class.forName("oracle.jdbc.OracleDriver");
		} catch (ClassNotFoundException e) {
			log.error(e);

		}
	}

	/**
	 * Return this class instance
	 * 
	 * @return QueryProcessorUtil
	 */
	public static QueryProcessorUtil getInstance() {
		if (thisInstance == null) {
			thisInstance = new QueryProcessorUtil();
			serviceLocator = ServiceLocator.getInstance();
		}
		return thisInstance;
	}

	/**
	 * Function to get ejb local home for query manager
	 * 
	 * @return QueryManagerLocalHome
	 * @throws I2B2Exception
	 * @throws ServiceLocatorException
	 */
	public QueryManagerLocalHome getQueryManagerLocalHome()
			throws I2B2Exception, ServiceLocatorException {
		return (QueryManagerLocalHome) serviceLocator
				.getLocalHome(getPropertyValue(EJB_LOCAL_JNDI_QUERYMANAGER_PROPERTIES));
	}

	/**
	 * Function to get ejb local home for query info
	 * 
	 * @return
	 * @throws I2B2Exception
	 * @throws ServiceLocatorException
	 */
	public QueryInfoLocalHome getQueryInfoLocalHome() throws I2B2Exception,
			ServiceLocatorException {
		return (QueryInfoLocalHome) serviceLocator
				.getLocalHome(getPropertyValue(EJB_LOCAL_JNDI_QUERYINFO_PROPERTIES));
	}

	/**
	 * Function to get ejb local home for query run
	 * 
	 * @return
	 * @throws I2B2Exception
	 * @throws ServiceLocatorException
	 */
	public QueryRunLocalHome getQueryRunLocalHome() throws I2B2Exception,
			ServiceLocatorException {
		return (QueryRunLocalHome) serviceLocator
				.getLocalHome(getPropertyValue(EJB_LOCAL_JNDI_QUERYRUN_PROPERTIES));
	}

	/**
	 * Function to get ejb local home for query result
	 * 
	 * @return
	 * @throws I2B2Exception
	 * @throws ServiceLocatorException
	 */
	public QueryResultLocalHome getQueryResultLocalHome() throws I2B2Exception,
			ServiceLocatorException {
		return (QueryResultLocalHome) serviceLocator
				.getLocalHome(getPropertyValue(EJB_LOCAL_JNDI_QUERYRESULT_PROPERTIES));
	}

	/**
	 * Function to get ejb local home for pdo query
	 * 
	 * @return
	 * @throws I2B2Exception
	 * @throws ServiceLocatorException
	 */
	public PdoQueryLocalHome getPdoQueryLocalHome() throws I2B2Exception,
			ServiceLocatorException {
		return (PdoQueryLocalHome) serviceLocator
				.getLocalHome(getPropertyValue(EJB_LOCAL_JNDI_PDOQUERY_PROPERTIES));
	}

	/**
	 * Function to create spring bean factory
	 * 
	 * @return BeanFactory
	 */
	public BeanFactory getSpringBeanFactory() {
		if (beanFactory == null) {
			ClassPathXmlApplicationContext ctx = new ClassPathXmlApplicationContext(
					"crcapp/CRCApplicationContext.xml");
			beanFactory = ctx.getBeanFactory();
		}

		return beanFactory;
	}

	/**
	 * Function returns database connection from app server
	 * 
	 * @return
	 * @throws I2B2Exception
	 * @throws SQLException
	 */
	public Connection getConnection() throws I2B2Exception, SQLException {
		String dataSourceName = getPropertyValue(DATASOURCE_JNDI_PROPERTIES);
		dataSource = (DataSource) serviceLocator
				.getAppServerDataSource(dataSourceName);

		Connection conn = dataSource.getConnection();
		return conn;
	}

	/**
	 * Function returns database connection from app server
	 * 
	 * @return
	 * @throws I2B2Exception
	 * @throws SQLException
	 */
	public Connection getManualConnection() throws I2B2Exception, SQLException {
		String dbConnectionString = getPropertyValue(DATABASE_CONNECTION_STRING_PROPERTIES);
		String dbUser = getPropertyValue(DATABASE_CONNECTION_USER_PROPERTIES);
		String dbPassword = getPropertyValue(DATABASE_CONNECTION_PASSWORD_PROPERTIES);
		return DriverManager.getConnection(dbConnectionString, dbUser,
				dbPassword);
	}

	/**
	 * Function to return metadata schema name, which is specified in property
	 * file
	 * 
	 * @return String
	 * @throws I2B2Exception
	 */
	public String getMetaDataSchemaName() throws I2B2Exception {
		return getPropertyValue(METADATA_SCHEMA_NAME_PROPERTIES);
	}

	/**
	 * Get Project managment cell's service url
	 * 
	 * @return
	 * @throws I2B2Exception
	 */
	public String getProjectManagementCellUrl() throws I2B2Exception {
		return getPropertyValue(PMCELL_WS_URL_PROPERTIES);
	}

	/**
	 * Get Project management bypass flag
	 * 
	 * @return
	 * @throws I2B2Exception
	 */
	public boolean getProjectManagementByPassFlag() throws I2B2Exception {
		String pmByPassFlag = getPropertyValue(PMCELL_BYPASS_FLAG_PROPERTIES);
		if (pmByPassFlag == null) {
			return false;
		} else if (pmByPassFlag.trim().equalsIgnoreCase("true")) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Get Project management bypass project role
	 * 
	 * @return
	 * @throws I2B2Exception
	 */
	public String getProjectManagementByPassRole() throws I2B2Exception {
		return getPropertyValue(PMCELL_BYPASS_ROLE_PROPERTIES);
	}

	/**
	 * Get Project management bypass project
	 * 
	 * @return
	 * @throws I2B2Exception
	 */
	public String getProjectManagementByPassProject() throws I2B2Exception {
		return getPropertyValue(PMCELL_BYPASS_PROJECT_PROPERTIES);
	}

	public String getCRCDBLookupDataSource() throws I2B2Exception {
		return getPropertyValue(DS_LOOKUP_DATASOURCE_PROPERTIES);
	}

	public String getCRCDBLookupServerType() throws I2B2Exception {
		return getPropertyValue(DS_LOOKUP_SERVERTYPE_PROPERTIES);
	}

	public String getCRCDBLookupSchemaName() throws I2B2Exception {
		return getPropertyValue(DS_LOOKUP_SCHEMANAME_PROPERTIES);
	}

	public String getOntologyUrl() throws I2B2Exception {
		return getPropertyValue(ONTOLOGYCELL_WS_URL_PROPERTIES);
	}

	/**
	 * Return app server datasource
	 * 
	 * @return datasource
	 * @throws I2B2Exception
	 * @throws SQLException
	 */
	public DataSource getSpringDataSource(String dataSourceName)
			throws I2B2Exception {
		DataSource dataSource = (DataSource) getSpringBeanFactory().getBean(
				dataSourceName);

		return dataSource;

	}

	// ---------------------
	// private methods here
	// ---------------------

	/**
	 * Load application property file into memory
	 */
	private String getPropertyValue(String propertyName) throws I2B2Exception {
		if (appProperties == null) {

			try {
				Resource resource = new ClassPathResource("crcapp/"
						+ APPLICATION_PROPERTIES_FILENAME);
				PropertiesFactoryBean pfb = new PropertiesFactoryBean();
				pfb.setLocation(resource);
				pfb.afterPropertiesSet();
				appProperties = (Properties) pfb.getObject();
			} catch (IOException e) {
				throw new I2B2Exception("Application property file("
						+ APPLICATION_PROPERTIES_FILENAME
						+ ") missing entries or not loaded properly");
			}
			if (appProperties == null) {
				throw new I2B2Exception("Application property file("
						+ APPLICATION_PROPERTIES_FILENAME
						+ ") missing entries or not loaded properly");
			}
		}

		String propertyValue = appProperties.getProperty(propertyName);

		if ((propertyValue != null) && (propertyValue.trim().length() > 0)) {

		} else {
			throw new I2B2Exception("Application property file("
					+ APPLICATION_PROPERTIES_FILENAME + ") missing "
					+ propertyName + " entry");
		}

		return propertyValue;
	}

}

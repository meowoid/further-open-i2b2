/*
 * Copyright (c) 2006-2007 Massachusetts General Hospital
 * All rights reserved. This program and the accompanying materials
 * are made available under the terms of the i2b2 Software License v1.0
 * which accompanies this distribution.
 *
 * Contributors:
 *                 Raj Kuttan
 *                 Lori Phillips
 */
package edu.harvard.i2b2.ontology.util;

import java.io.IOException;
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


/**
 * This is the Ontology service's main utility class
 * This utility class provides support for
 * fetching resources like datasouce, to read application
 * properties, to get ejb home,etc.
 * $Id: OntologyUtil.java,v 1.15 2009/01/08 19:27:01 lcp5 Exp $
 * @author rkuttan
 */
public class OntologyUtil {
    /** property file name which holds application directory name **/
    public static final String APPLICATION_DIRECTORY_PROPERTIES_FILENAME = "ontology_application_directory.properties";

    /** application directory property name **/
    public static final String APPLICATIONDIR_PROPERTIES = "edu.harvard.i2b2.ontology.applicationdir";

    /** application property filename**/
    public static final String APPLICATION_PROPERTIES_FILENAME = "ontology.properties";

    /** property name for datasource present in app property file**/
    private static final String DATASOURCE_JNDI_PROPERTIES = "ontology.jndi.datasource_name";

    /** property name for metadata schema name**/
    private static final String METADATA_SCHEMA_NAME_PROPERTIES = "ontology.bootstrapdb.metadataschema";

    /** spring bean name for datasource **/
    private static final String DATASOURCE_BEAN_NAME = "dataSource";

    /** property name for PM endpoint reference **/
    private static final String PM_WS_EPR = "ontology.ws.pm.url";
    
    /** property name for PM webservice method **/
    private static final String PM_WS_METHOD = "ontology.ws.pm.webServiceMethod";

    /** property name for PM bypass **/
    private static final String PM_BYPASS = "ontology.ws.pm.bypass";

    /** property name for PM bypass project **/
    private static final String PM_BYPASS_PROJECT = "ontology.ws.pm.bypass.project";

    /** property name for PM bypass role **/
    private static final String PM_BYPASS_ROLE = "ontology.ws.pm.bypass.role";

    /** property name for ONT_TERM_DELIMITER **/
    private static final String ONT_TERM_DELIMITER = "ontology.terminal.delimiter";
    
    
    /** class instance field**/
    private static OntologyUtil thisInstance = null;

    /** service locator field**/
    private static ServiceLocator serviceLocator = null;

    /** field to store application properties **/
    private static Properties appProperties = null;

    /** log **/
    protected final Log log = LogFactory.getLog(getClass());

    /** field to store app datasource**/
    private DataSource dataSource = null;

    /** single instance of spring bean factory**/
    private BeanFactory beanFactory = null;

    /**
     * Private constructor to make the class singleton
     */
    private OntologyUtil() {
    }

    /**
     * Return this class instance
     * @return OntologyUtil
     */
    public static OntologyUtil getInstance() {
        if (thisInstance == null) {
            thisInstance = new OntologyUtil();
        }

        serviceLocator = ServiceLocator.getInstance();

        return thisInstance;
    }

    /**
     * Return the ontology spring config
     * @return
     */
    public BeanFactory getSpringBeanFactory() {
        if (beanFactory == null) {           
            ClassPathXmlApplicationContext ctx = new ClassPathXmlApplicationContext("ontologyapp/OntologyApplicationContext.xml");
            beanFactory = ctx.getBeanFactory();
        }

        return beanFactory;
    }

    /**
     * Return metadata schema name
     * @return
     * @throws I2B2Exception
     */
    public String getMetaDataSchemaName() throws I2B2Exception {
        return getPropertyValue(METADATA_SCHEMA_NAME_PROPERTIES).trim()+ ".";
    }

    /**
     * Return PM cell endpoint reference URL
     * @return
     * @throws I2B2Exception
     */
    public String getPmEndpointReference() throws I2B2Exception {
        return getPropertyValue(PM_WS_EPR).trim();
    }

    /**
     * Return PM cell web service method
     * @return
     * @throws I2B2Exception
     */
    public String getPmWebServiceMethod() throws I2B2Exception {
        return getPropertyValue(PM_WS_METHOD).trim();
    }
    
    /**
     * Return PM bypass flag
     * @return
     * @throws I2B2Exception
     */
    public Boolean isPmBypass() throws I2B2Exception {
        return Boolean.valueOf(getPropertyValue(PM_BYPASS).trim());
    }

    /**
     * Return PM bypass project name
     * @return
     * @throws I2B2Exception
     */
    public String getPmBypassProject() throws I2B2Exception {
        return getPropertyValue(PM_BYPASS_PROJECT).trim();
    }

    /**
     * Return PM bypass role assignment
     * @return
     * @throws I2B2Exception
     */
    public String getPmBypassRole() throws I2B2Exception {
        return getPropertyValue(PM_BYPASS_ROLE).trim();
    }

    /**
     * Return Ontology terminal delimiter
     * @return
     * @throws I2B2Exception
     */
    public Boolean getOntTerminalDelimiter() throws I2B2Exception {
        return Boolean.valueOf(getPropertyValue(ONT_TERM_DELIMITER).trim());
    }
    
    
    /**
     * Return app server datasource
     * @return datasource
     * @throws I2B2Exception
     * @throws SQLException
     */
    public DataSource getDataSource(String dataSourceName) throws I2B2Exception {
        //DataSource dataSource = (DataSource) getSpringBeanFactory()
                                              //   .getBean(DATASOURCE_BEAN_NAME);
    	
    	dataSource = (DataSource) serviceLocator.getAppServerDataSource(dataSourceName);
    	return dataSource;
  
    }

    

    //---------------------
    // private methods here
    //---------------------

    /**
     * Load application property file into memory
     */
    private String getPropertyValue(String propertyName)
        throws I2B2Exception {
        if (appProperties == null) {
            
            try {
                Resource resource = new ClassPathResource("ontologyapp/" + APPLICATION_PROPERTIES_FILENAME);
                PropertiesFactoryBean pfb = new PropertiesFactoryBean();
                pfb.setLocation(resource);
                pfb.afterPropertiesSet();
                appProperties = (Properties) pfb.getObject();
            } catch (IOException e) {
                throw new I2B2Exception("Application property file(" +
                	APPLICATION_PROPERTIES_FILENAME +
                    ") missing entries or not loaded properly");
            }

            if (appProperties == null) {
                throw new I2B2Exception("Application property file(" +
                	APPLICATION_PROPERTIES_FILENAME +
                    ") missing entries or not loaded properly");
            }
        }

        String propertyValue = appProperties.getProperty(propertyName);

        if ((propertyValue != null) && (propertyValue.trim().length() > 0)) {
            
        } else {
            throw new I2B2Exception("Application property file(" +
                APPLICATION_PROPERTIES_FILENAME + ") missing " + propertyName +
                " entry");
        }

        return propertyValue;
    }
}

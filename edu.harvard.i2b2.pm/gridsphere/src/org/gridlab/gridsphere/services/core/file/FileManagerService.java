/*
 * @author <a href="mailto:novotny@aei.mpg.de">Jason Novotny</a>
 * @version $Id: FileManagerService.java,v 1.1 2007/08/24 17:24:53 mem61 Exp $
 */
package org.gridlab.gridsphere.services.core.file;

import org.gridlab.gridsphere.portlet.User;
import org.gridlab.gridsphere.portlet.service.PortletService;

import java.io.File;
import java.io.FileNotFoundException;

/**
 * The <code>UserStorageService</code> manages a temporary directory used for the storage of
 * user's persistent data.
 */
public interface FileManagerService extends PortletService {

    public String getLocationPath(User user, String fileName);

    //public void storeFile(User user, FileInputBean file, String fileName) throws IOException, Exception;

    public void deleteFile(User user, String fileName);

    public File getFile(User user, String fileName) throws FileNotFoundException;

    public String[] getUserFileList(User user);

}

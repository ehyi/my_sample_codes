using System;
using System.Collections.Generic;
using System.Data.SqlClient;
using System.IO;
using System.Linq;
using System.Text;

namespace portal_cleanup
{
    class Program
    {
        private const string constSQLConnectionSring_dev =
            "server=localhost\\MSSQLSERVERR2;" +
            "user id=sa;" +
            "password=*****;" +
            "initial catalog=mydatabase; " +
            "connection timeout=10";

        private const string constSQLConnectionSring_live =
            "server=localhost\\MSSQLSERVERR2;" +
            "user id=sa;" +
            "password=*****;" +
            "initial catalog=mydatabase; " +
            "connection timeout=10";

        private static string strPortalPath = ":\\mydomain";
        private static string strMoveToPath = ":\\ArchivedByEugene\\mydomain\\";
        private static string strLogFile = ":\\ArchivedByEugene\\portal_cleanup_log_DATETIME.txt";

        private static string strSQLConnectionSring = "";
        private string strText = "";

        static void Main(string[] args)
        {
            string strDestinationPath;
            string strLine;
            string strDefaultFilePath;
            string strDrive;

            string strDateTime = string.Format("{0:yyyyMMdd_HHmm}", DateTime.Now);

            if (args.Count() < 1)
            {
                Console.WriteLine("Parameters missing: <environment: dev/live>");
                Environment.Exit(0);
            }
            else
            {
                string strEnv = args[0].Trim();

                if (strEnv.ToLower() == "live")
                {
                    strSQLConnectionSring = constSQLConnectionSring_live;
                    strDrive = "D";

                    strPortalPath = strDrive + strPortalPath;
                    strMoveToPath = strDrive + strMoveToPath;
                    strLogFile = strDrive + strLogFile;
                }
                else
                {
                    strSQLConnectionSring = constSQLConnectionSring_dev;
                    strDrive = "D";

                    strPortalPath = "D:\\work\\mydomain";
                    strMoveToPath = "D:\\work\\ArchivedByEugene\\mydomain\\";
                    strLogFile = "D:\\work\\ArchivedByEugene\\portal_cleanup_log_DATETIME.txt";
                }
            }

            strLogFile = strLogFile.Replace("DATETIME", strDateTime);

            // Test SQL connection
            SqlConnection myConnection = new SqlConnection(strSQLConnectionSring);
            try
            {
                myConnection.Open();
                myConnection.Close();
            }
            catch (SqlException ex)
            {
                Console.WriteLine("SQL connection failed: " + strSQLConnectionSring + "\n\n");
                Console.WriteLine("Press any key to exit.");
                Console.ReadKey();
                System.Environment.Exit(1);
            }

            System.IO.StreamWriter file = new System.IO.StreamWriter(strLogFile);

            string[] arrFolders = Directory.GetDirectories(@strPortalPath);
            foreach (string strOneFolder in arrFolders)
            {
                string[] strParts = strOneFolder.Split('\\');
                string strFolderName = strParts[strParts.Length - 1];

                // Skip these known system folders
                switch (strFolderName)
                {
                    case ".svn":
                    case "admin":
                    case "aspnet_client":
                    case "App_Browsers":
                    case "App_Code":
                    case "App_Data":
                    case "App_GlobalResources":
                    case "App_Themes":
                    case "bin":
                    case "Components":
                    case "Config":
                    case "controls":
                    case "DesktopModules":
                    case "Documentation":
                    case "images":
                    case "Install":
                    case "js":
                    case "Portals":
                    case "Providers":
                    case "Resources":
                        continue;
                        break;
                }

                // In case I missed above
                strDefaultFilePath = strOneFolder + "\\Default.aspx";
                if (!File.Exists(strOneFolder + "\\Default.aspx"))
                {
                    file.WriteLine("* Skipping: " + strDefaultFilePath + ".  Because there is no Default.aspx file.");
                    continue;
                }

                if (QueryEbookPath(strFolderName) == 0)
                {
                    strLine = strFolderName + " -> NOT FOUND";
                    Console.WriteLine(strLine);
                    file.WriteLine(strLine);
                    try
                    {
                        strDestinationPath = strMoveToPath + strFolderName;
                        System.IO.Directory.Move(strOneFolder, strDestinationPath);

                        strLine = " * " + strOneFolder + " moved to " + strDestinationPath + "\n";
                        Console.WriteLine(strLine);
                        file.WriteLine(strLine);
                    }
                    catch (SqlException ex)
                    {
                        Console.WriteLine("Directory Move failed. " + ex.Message);
                    }  

                }
                else if (QueryEbookPath(strFolderName) == 1)
                {
                    strLine = strFolderName + " -> found\n";
                    Console.WriteLine(strLine);
                    //file.WriteLine(strLine);
                }
                else if (QueryEbookPath(strFolderName) == -1)
                {
                    strLine = "SQL connection error.";
                    Console.WriteLine(strLine);
                }
            }

            file.Close();

            Console.WriteLine("Press any key to exit.");
            Console.ReadKey();
        }

        static int QueryEbookPath(string strFolderName)
        {
            SqlConnection myConnection = new SqlConnection(strSQLConnectionSring);
            try
            {
                myConnection.Open();

                strFolderName = strFolderName.Replace("'", "");

                SqlDataReader myReader = null;
                string strQuery =
                    "select count(*) as count1 " +
                    "from pd_Portals " +
                    "where homedirectory like '%/" + strFolderName + "%'";
                SqlCommand myCommand = new SqlCommand(strQuery, myConnection);
                myReader = myCommand.ExecuteReader();
                if (myReader.Read())
                {
                    if ((int)myReader["count1"] > 0)
                    {
                        return 1;
                    }
                    else
                    {
                        return 0;
                    }
                }
            }
            catch (SqlException ex)
            {
                Console.WriteLine("SQL connection failed. " + ex.Message);
            }
            finally
            {

                myConnection.Close();
            }

            return -1;
        }
    }
}

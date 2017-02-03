using System;
using System.Collections.Generic;
using System.Data;
using System.Data.OleDb;
using System.Data.SqlClient;
using System.IO;
using System.Linq;
using System.Net.Mail;
using System.Net;
using System.Text;
using Microsoft.VisualBasic.FileIO;
using Tamir.SharpSsh;

namespace ImportAutomation
{
    class Program
    {

        private const string constSQLConnectionSring_dev = 
            "server=EUGENE-PC\\SQL2008Instance;" +
            "user id=sa;" +
            "password=*****;" +
            "initial catalog=mydatabase; " +
            "connection timeout=10";

        private const string constSQLConnectionSring_test =
            "server=localhost;" +
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

        private static string strSQLConnectionSring = "";

        private const string constMail_Host = "smtp.gmail.com";
        private const int constMail_Port = 587;
        private const string constMail_SMTPUser = "webmaster@mydomain.com";
        private const string constMail_SMTPPass = "*****";
        private const string constMail_From = "webmaster@mydomain.com";
        private const string constMail_Subject = "mydomain.com Import ";

        private static string[] arrMail_To_Live = new string[] 
            { 
                "eyi@perfectionlearning.com",
                "lpreston@perfectionlearning.com"
            };
        private static string[] arrMail_To_Dev = new string[] 
            { 
                "eyi@perfectionlearning.com"
            };
        private static string[] arrMail_To = new string[10];

        private const string constSFTPHost = "files.mydomain.com";
        private const string constSFTPUser_Live = "john";
        private const string constSFTPPass_Live = "*****";
        private const string constSFTPPath_Live = "/home/john/files/";

        private const string constSFTPUser_Dev = "eyi";
        private const string constSFTPPass_Dev = "*****";
        private const string constSFTPPath_Dev = "/home/eyi/files/";

        private static string constSFTPUser = "";
        private static string constSFTPPass = "";
        private static string constSFTPPath = "";

        private static string[] arrImportHeader = new string[] 
            { 
                "ILA_DistrictCode",         // 0
                "DistrictName",             // 1
                "SchoolID",                 // 2
                "SchoolName",               // 3
                "SchoolAddress1",           // 4
                "SchoolAddress2",           // 5
                "SchoolCity",               // 6
                "SchoolState",              // 7
                "SchoolZip",                // 8
                "SchoolPhone",              // 9
                "SchoolContactFirstName",   // 10
                "SchoolContactLastName",    // 11
                "SchoolContactEmail",       // 12
                "ClassID",                  // 13
                "ClassName",                // 14
                "TeacherID",                // 15
                "TeacherFirstName",         // 16
                "TeacherLastName",          // 17
                "TeacherEmail",             // 18
                "TeacherUserName",          // 19
                "TeacherPassword",          // 20
                "StudentID",                // 21
                "StudentFirstName",         // 22
                "StudentLastName",          // 23      
                "StudentEmail",             // 24
                "StudentUserName",          // 25
                "StudentPassword"           // 26
            };

        private static int[] arrRequired = new int[100];

        private const string constPattern = @"^[a-z|0-9|]*([_][a-z|0-9]+)*([.][a-z|" + 
            @"0-9]+([_][a-z|0-9]+)*)?@[a-z][a-z|0-9|]*\.([a-z]" + 
            @"[a-z|0-9]*(\.[a-z][a-z|0-9]*)*)$";
        private const string constValidFileExt = ".csv";
        private const string strApplicationName = "DotNetNuke";
        private const int intCreatedByUserID = 0;
        private const string strError = "[ERROR] ";
        private const int constPasswordLengthRequired = 6;

        private static string strFileName = "";
        private static string strLogFile = "logs\\Import_log_DATETIME.txt";
        private static string strLogFileDistrict = "logs\\Import_log_for_district_DATETIME.txt";
        private static string strTextLine = "";
        private static string strDistrictAdminEmail = "";
        private static Boolean booNewUserAdded = false;
        private static int intNumOfUserSchool = 1000;
        private static int intNumOfUserClass = 100;
        private static int intAddedSchools = 0;
        private static int intAddedClasses = 0;
        private static int intAddedTeachers = 0;
        private static int intAddedStudents = 0;
        private static int intProcessedStudents = 0;

        private static StreamWriter fileLog;
        private static StreamWriter fileLogDistrict;

        static void Main(string[] args)
        {
            arrRequired[0] = 1;
            arrRequired[1] = 1;
            arrRequired[2] = 1;
            arrRequired[3] = 1;
            arrRequired[13] = 1;
            arrRequired[14] = 1;
            arrRequired[15] = 1;
            arrRequired[16] = 1;
            arrRequired[17] = 1;
            arrRequired[18] = 1;
            arrRequired[19] = 1;
            arrRequired[20] = 1;
            arrRequired[21] = 1;
            arrRequired[22] = 1;
            arrRequired[23] = 1;
            arrRequired[25] = 1;
            arrRequired[26] = 1;

            string strDateTime = string.Format("{0:yyyyMMdd_HHmm}", DateTime.Now);
            strLogFile = strLogFile.Replace("DATETIME", strDateTime);
            strLogFileDistrict = strLogFileDistrict.Replace("DATETIME", strDateTime);

            if (args.Count() < 2)
            {
                fileLog = new StreamWriter(strLogFile);
                strTextLine = strError + "Parameters missing: <input file name> <environment: dev/test/live>";
                fileLog.WriteLine(strTextLine);
                goto Finish;
                //Environment.Exit(0);
            }
            else
            {
                strFileName = args[0].Trim();
                strSQLConnectionSring = args[1].Trim();

                if (strSQLConnectionSring.ToLower() == "live")
                {
                    strSQLConnectionSring = constSQLConnectionSring_live;
                    arrMail_To = arrMail_To_Live;
                    Array.Resize(ref arrMail_To, arrMail_To_Live.Count());
                    constSFTPUser = constSFTPUser_Live;
                    constSFTPPass = constSFTPPass_Live;
                    constSFTPPath = constSFTPPath_Live;
                }
                else if (strSQLConnectionSring.ToLower() == "test")
                {
                    strSQLConnectionSring = constSQLConnectionSring_test;
                    arrMail_To = arrMail_To_Live;
                    Array.Resize(ref arrMail_To, arrMail_To_Live.Count());
                    constSFTPUser = constSFTPUser_Live;
                    constSFTPPass = constSFTPPass_Live;
                    constSFTPPath = constSFTPPath_Live;
                }
                else
                {
                    strSQLConnectionSring = constSQLConnectionSring_dev;
                    arrMail_To = arrMail_To_Dev;
                    Array.Resize(ref arrMail_To, arrMail_To_Dev.Count());
                    constSFTPUser = constSFTPUser_Dev;
                    constSFTPPass = constSFTPPass_Dev;
                    constSFTPPath = constSFTPPath_Dev;
                }
            }

            if (DownloadFile() == 1)
            {
                fileLog = new StreamWriter(strLogFile);
                strTextLine = "File downloaded: " + constSFTPPath + strFileName + "\r\n";
                fileLog.WriteLine(strTextLine);
            }
            else if (DownloadFile() == 2)
            {
                // File does not exist.  Silently quit.
                Environment.Exit(0);
            }
            else
            {
                fileLog = new StreamWriter(strLogFile);
                strTextLine = strError + "File download failed: " + constSFTPPath + strFileName;
                fileLog.WriteLine(strTextLine);
                goto Finish;
            }

            fileLogDistrict = new StreamWriter(strLogFileDistrict);

            strTextLine = "Input file name: " + strFileName + "\r\n";
            fileLog.WriteLine(strTextLine);

            string strExt = Path.GetExtension(strFileName);
            if (strExt != constValidFileExt)
            {
                strTextLine = strError + "Invalid input file extension: " + strExt;
                fileLog.WriteLine(strTextLine);
                goto Finish;
            }

            // Read an Excel file.
            /*
            string strSheetName = "Sheet1";
            string strExcelConnection = @"Provider=Microsoft.Jet.OLEDB.4.0;Data Source=" +
                    strFileName + ";Extended Properties='Excel 8.0;HDR=YES;'";
            DataSet dsUsers = new DataSet();
            using (OleDbConnection myConnection = new OleDbConnection(strExcelConnection))
            {
                OleDbDataAdapter daUsers = new OleDbDataAdapter("Select * FROM [" + strSheetName + "$]", myConnection);
                daUsers.Fill(dsUsers);
            }
            if (dsUsers.Tables[0].Rows.Count > 0)
            {
                intUsercnt = dsUsers.Tables[0].Rows.Count;
                Console.WriteLine("intUsercnt(" + intUsercnt.ToString() + ")");
            }
            else
            {
                strError += "No rows to process in " + strFileName;
            }
            */

            // Read a CSV file.  Requires Microsoft.VisualBasic.FileIO in Preferences.
            TextFieldParser parser = new TextFieldParser(@strFileName);
            parser.TextFieldType = FieldType.Delimited;
            parser.SetDelimiters(",");

            int i = 0;
            int intDistrictID = 0;
            int intSchoolID = 0;
            int intClassID = 0;
            int intTeacherID = 0;
            int intStudentID = 0;
            int intRole = 0;
            int intSchoolLicInfo = 0;
            string strUserType = "";
            bool booMissingRequired = false;

            while (!parser.EndOfData)
            {
                booMissingRequired = false;

                fileLog.WriteLine("Row[" + i.ToString() + "]");
                Console.Write(i.ToString() + " ");

                string[] arrFields = parser.ReadFields();
                int j = 0;
                string[] arrRow = new string[arrImportHeader.Count()];

                foreach (string strField in arrFields)
                {
                    fileLog.WriteLine(" Column[" + j.ToString() + "]: " + strField);

                    // Validate the columns.
                    if (i == 0)
                    {
                        if (strField != arrImportHeader[j])
                        {
                            strTextLine = strError + "Invalid column in the input file: " + strField;
                            fileLog.WriteLine(strTextLine);
                            goto Finish;
                        }

                    }
                    else
                    {
                        arrRow[j] = strField.Trim();
                    }

                    j++;
                }

                if (i != 0)
                {
                    for (int idxRow = 0; idxRow < arrRow.Count(); idxRow++)
                    {
                        if (arrRow[idxRow] == "" && arrRequired[idxRow] == 1)
                        {
                            strTextLine = strError + "Missing a required value: " + arrImportHeader[idxRow] + ". Skipping the row.";
                            fileLog.WriteLine(strTextLine);
                            fileLogDistrict.WriteLine(strTextLine);
                            booMissingRequired = true;                            
                        }
                    }

                    if (booMissingRequired == true)
                    {
                        intProcessedStudents++;
                        i++;
                        continue;
                    }

                    intDistrictID = GetDistrictID(arrRow);
                    if (intDistrictID == -1)
                    {
                        strTextLine = strError + "Invalid District: " + arrRow[0];
                        fileLog.WriteLine(strTextLine);
                        continue;
                    }
                    else
                    {
                        strTextLine = "District: " + arrRow[0] + " (ID: " + intDistrictID.ToString() + ")";
                        fileLog.WriteLine(strTextLine);
                    }

                    intSchoolID = GetSchoolID(arrRow, intDistrictID);
                    if (intSchoolID == -1)
                    {
                        strTextLine = strError + "Invalid School: " + arrRow[3];
                        fileLog.WriteLine(strTextLine);
                        continue;
                    }
                    else
                    {
                        strTextLine = "School: " + arrRow[3] + " (ID: " + intSchoolID.ToString() + ")";
                        fileLog.WriteLine(strTextLine);
                    }

                    // Teacher
                    strUserType = "Teacher";
                    booNewUserAdded = false;
                    intTeacherID = GetUser(i, arrRow, intDistrictID, strUserType);
                    if (intTeacherID == -2)
                    {
                        strTextLine = strError + "Teacher username already exists with another district: " + arrRow[18];
                        fileLog.WriteLine(strTextLine);
                        continue;
                    }
                    else if (intTeacherID == -1)
                    {
                        strTextLine = strError + "Unable to add teacher username: " + arrRow[18];
                        fileLog.WriteLine(strTextLine);
                        continue;
                    }
                    else
                    {
                        strTextLine = "Teacher: " + arrRow[19] + " (ID: " + intTeacherID.ToString() + ")";
                        fileLog.WriteLine(strTextLine);
                    }

                    // Class
                    intClassID = GetClassID(i, arrRow, intSchoolID, intTeacherID);
                    if (intClassID == -1)
                    {
                        strTextLine = strError + "Unable to add class: " + arrRow[14];
                        fileLog.WriteLine(strTextLine);
                        continue;
                    }
                    else
                    {
                        strTextLine = "Class: " + arrRow[14] + " (ID: " + intClassID.ToString() + ")";
                        fileLog.WriteLine(strTextLine);
                    }

                    // Teacher role
                    intRole = AddUserRole(i, intTeacherID, strUserType, intDistrictID, intSchoolID, intClassID);
                    if (intRole == 0)
                    {
                        strTextLine = strError + "Unable to assign teacher role of Techer ID: " + intTeacherID.ToString();
                        fileLog.WriteLine(strTextLine);
                        continue;
                    }

                    // School Lic Update
                    if (booNewUserAdded == true)
                    {
                        intSchoolLicInfo = UpdateSchoolClassLicense(i, intTeacherID, strUserType, intDistrictID, intSchoolID, intClassID);
                        if (intSchoolLicInfo == -1)
                        {
                            strTextLine = strError + "Unable to update school license for Techer ID: " + intTeacherID.ToString();
                            fileLog.WriteLine(strTextLine);
                            continue;
                        }
                    }

                    // Student
                    strUserType = "Student";
                    booNewUserAdded = false;
                    intStudentID = GetUser(i, arrRow, intDistrictID, strUserType);
                    intProcessedStudents++;
                    if (intStudentID == -2)
                    {
                        strTextLine = strError + "Student username already exists with another district: " + arrRow[25];
                        fileLog.WriteLine(strTextLine);
                        continue;
                    }
                    else if (intStudentID == -1)
                    {
                        strTextLine = strError + "Unable to add student username: " + arrRow[25];
                        fileLog.WriteLine(strTextLine);
                        continue;
                    }
                    else
                    {
                        strTextLine = "Student: " + arrRow[25] + " (ID: " + intStudentID.ToString() + ")";
                        fileLog.WriteLine(strTextLine);
                    }

                    // Student role
                    intRole = AddUserRole(i, intStudentID, strUserType, intDistrictID, intSchoolID, intClassID);
                    if (intRole == 0)
                    {
                        strTextLine = strError + "Unable to assign student role of Student ID: " + intStudentID.ToString();
                        fileLog.WriteLine(strTextLine);
                        continue;
                    }

                    // Class Lic Update
                    if (booNewUserAdded == true)
                    {
                        intSchoolLicInfo = UpdateSchoolClassLicense(i, intStudentID, strUserType, intDistrictID, intSchoolID, intClassID);
                        if (intSchoolLicInfo == -1)
                        {
                            strTextLine = strError + "Unable to update class license for Student ID: " + intStudentID.ToString();
                            fileLog.WriteLine(strTextLine);
                            continue;
                        }
                    }
                }

                intDistrictID = 0;
                intSchoolID = 0;
                intClassID = 0;
                intTeacherID = 0;
                intStudentID = 0;
                intRole = 0;
                strUserType = "";
                booNewUserAdded = false;

                i++;
            }

            /*
             * Finale
             */
            parser.Close();

            if (strFileName.Trim() != "")
            {
                try
                {
                    string strFileMoveDestination = "files/" + strFileName + "_" + strDateTime;
                    File.Move(strFileName, strFileMoveDestination);
                    strTextLine = "Input file moved to: " + strFileMoveDestination;
                    fileLog.WriteLine(" ");
                    fileLog.WriteLine(" ");
                    fileLog.WriteLine(strTextLine);
                }
                catch (IOException ex)
                {
                    strTextLine = strError + "Input file move: " + ex.Message;
                    Console.WriteLine(strTextLine);
                    fileLog.WriteLine(strTextLine);
                }
            }

            strTextLine = "\r\n\r\n";
            strTextLine += "Number of schools added: " + intAddedSchools.ToString() + "\r\n";
            strTextLine += "Number of classes added: " + intAddedClasses.ToString() + "\r\n";
            strTextLine += "Number of teachers added: " + intAddedTeachers.ToString() + "\r\n";
            strTextLine += "Number of students added: " + intAddedStudents.ToString() + "\r\n";
            strTextLine += "Number of rows and students processed: " + intProcessedStudents.ToString() + "\r\n";
            fileLog.WriteLine(strTextLine);
            fileLogDistrict.WriteLine(strTextLine);
            fileLogDistrict.Close();

            // Email the district
            string strSubject = constMail_Subject + strDateTime;
            if (intProcessedStudents > 0)
            {
                string strBodyDistrict = System.IO.File.ReadAllText(strLogFileDistrict);

                if (strDistrictAdminEmail.Trim() != "")
                {
                    SendMail(constMail_From, strDistrictAdminEmail, strSubject, strBodyDistrict);
                    strTextLine = "\r\n" + "Email sent to the district admin: " + strDistrictAdminEmail + "\r\n";
                }
                else
                {
                    strTextLine = "\r\n" + "Email NOT sent to the district admin because no email found.\r\n";
                }
                fileLog.WriteLine(strTextLine);

                foreach (string mailTo in arrMail_To)
                {
                    if (mailTo.Trim() != "")
                    {
                        SendMail(constMail_From, mailTo, strSubject, strBodyDistrict);
                    }
                }
            }

            Finish:

            fileLog.Close();
            strSubject = "[ADMIN] " + constMail_Subject + strDateTime;
            string strBody = System.IO.File.ReadAllText(strLogFile);
            foreach (string mailTo in arrMail_To) 
            {
                if (mailTo.Trim() != "")
                {
                    SendMail(constMail_From, mailTo, strSubject, strBody);
                }
            }


            //Console.WriteLine("Press any key to exit.");
            //Console.ReadKey();
        }


        static int GetDistrictID(string[] arrRow)
        {
            int intPortalID = -1;
            int intAdministratorID = -1;

            SqlConnection myConnection = new SqlConnection(strSQLConnectionSring);
            try
            {
                string strQuery =
                    "select PortalID, administratorid " +
                    "from pd_Portals " +
                    "where DistrictName = '" + arrRow[0] + "'";
                //Console.WriteLine("strQuery 1(" + strQuery + ")");
                myConnection.Open();
                SqlCommand myCommand = new SqlCommand(strQuery, myConnection);
                SqlDataReader myReader = myCommand.ExecuteReader();
                while (myReader.Read())
                {
                    intPortalID = (int)myReader["PortalID"];
                    intAdministratorID = (int)myReader["administratorid"];
                }
                myReader.Dispose();
                myCommand.Dispose();
                myConnection.Close();

                strQuery = @"
                    select
                    email
                    from pd_users
                    where userid = @userid
                    ";
                //Console.WriteLine("strQuery 2(" + strQuery + ")(" + intAdministratorID.ToString() + ")");
                myConnection.Open();
                myCommand = new SqlCommand(strQuery, myConnection);
                myCommand.Parameters.Clear();
                myCommand.Parameters.AddWithValue("@userid", intAdministratorID);
                myReader = myCommand.ExecuteReader();
                while (myReader.Read())
                {
                    strDistrictAdminEmail = myReader["email"].ToString();
                }
                myReader.Dispose();
                myCommand.Dispose();
                myConnection.Close();
            }
            catch (SqlException ex)
            {
                strTextLine = strError + "SQL Error in GetDistrictID: " + ex.Message;
                Console.WriteLine(strTextLine);
                fileLog.WriteLine(strTextLine);
            }
            finally
            {
                myConnection.Close();
            }

            return intPortalID;
        }

        static int GetSchoolID(string[] arrRow, int intDistrictID)
        {
            int intSchoolID = -1;
            int intCountyID = 221;
            int intUserCreated = 2;
            int intStatus = 1;
            int intDadminID = 0;
            string strSchoolName = arrRow[3];
            string strAddress1 = arrRow[4];
            string strAddress2 = arrRow[5];
            string strCity = arrRow[6];
            string strState = arrRow[7];
            string strZip = arrRow[8];
            string strPhone = arrRow[9];
            string strContactLastName = arrRow[11];
            string strContactFirstName = arrRow[10];
            string strContactEmail = arrRow[12];
            string strCustomerNumber = "";
            int intRoleID = 4;

            SqlConnection myConnection = new SqlConnection(strSQLConnectionSring);
            try
            {
                string strQuery = "select ID " +
                    "from ILA_SCHOOL " +
                    "where SCHOOL_NAME = '" + strSchoolName + "' " +
                    "and CITY = '" + strCity + "' ";
                //Console.WriteLine("strQuery(" + strQuery + ")");

                myConnection.Open();
                using (SqlCommand myCommand = new SqlCommand(strQuery, myConnection))
                using (SqlDataReader myReader = myCommand.ExecuteReader())
                {
                    while (myReader.Read())
                    {
                        intSchoolID = (int)myReader["ID"];
                        strTextLine = "School already exists: " + strSchoolName + " (ID: " + intSchoolID + ")";
                        fileLog.WriteLine(strTextLine);
                    }
                }
                myConnection.Close();

                if (intSchoolID == -1)
                {
                    strQuery = @" 
                        insert into ILA_SCHOOL (
                        SCHOOL_NAME,
                        ADDRESS_LINE1,
                        ADDRESS_LINE2,
                        CITY,
                        STATE,
                        COUNTRY_ID,
                        POSTAL_CODE,
                        CUSTOMER_NUMBER,
                        TELEPHONE,
                        CONTACT_LAST_NAME,
                        CONTACT_FIRST_NAME,
                        CONTACT_EMAIL,
                        PORTAL_ID,
                        DADMIN_ID,
                        USER_CREATED,
                        STATUS
                        ) values (
                        @SCHOOL_NAME,
                        @ADDRESS_LINE1,
                        @ADDRESS_LINE2,
                        @CITY,
                        @STATE,
                        @COUNTRY_ID,
                        @POSTAL_CODE,
                        @CUSTOMER_NUMBER,
                        @TELEPHONE,
                        @CONTACT_LAST_NAME,
                        @CONTACT_FIRST_NAME,
                        @CONTACT_EMAIL,
                        @PORTAL_ID,
                        @DADMIN_ID,
                        @USER_CREATED,
                        @STATUS
                        );SELECT SCOPE_IDENTITY();";
                    myConnection.Open();
                    SqlCommand myCommand = new SqlCommand(strQuery, myConnection);
                    myCommand.Parameters.Clear();
                    myCommand.Parameters.AddWithValue("@SCHOOL_NAME", strSchoolName);
                    myCommand.Parameters.AddWithValue("@ADDRESS_LINE1", strAddress1);
                    myCommand.Parameters.AddWithValue("@ADDRESS_LINE2", strAddress2);
                    myCommand.Parameters.AddWithValue("@CITY", strCity);
                    myCommand.Parameters.AddWithValue("@STATE", strState);
                    myCommand.Parameters.AddWithValue("@COUNTRY_ID", intCountyID);
                    myCommand.Parameters.AddWithValue("@POSTAL_CODE", strZip);
                    myCommand.Parameters.AddWithValue("@CUSTOMER_NUMBER", strCustomerNumber);
                    myCommand.Parameters.AddWithValue("@TELEPHONE", strPhone);
                    myCommand.Parameters.AddWithValue("@CONTACT_LAST_NAME", strContactLastName);
                    myCommand.Parameters.AddWithValue("@CONTACT_FIRST_NAME", strContactFirstName);
                    myCommand.Parameters.AddWithValue("@CONTACT_EMAIL", strContactEmail);
                    myCommand.Parameters.AddWithValue("@PORTAL_ID", intDistrictID);
                    myCommand.Parameters.AddWithValue("@DADMIN_ID", intDadminID);
                    myCommand.Parameters.AddWithValue("@USER_CREATED", intUserCreated);
                    myCommand.Parameters.AddWithValue("@STATUS", intStatus);
                    intSchoolID = (int)(decimal)myCommand.ExecuteScalar();
                    myCommand.Dispose();
                    myConnection.Close();

                    strTextLine = "School Added: " + strSchoolName;
                    fileLog.WriteLine(strTextLine);
                    fileLogDistrict.WriteLine(strTextLine);
                    intAddedSchools++;

                    // ILA_SCHOOL_LIC_INFO
                    strQuery = @"ILA_UID_SCHOOL_LIC_INFO";
                    myCommand = new SqlCommand(strQuery, myConnection);
                    myCommand.CommandType = CommandType.StoredProcedure;
                    myCommand.Parameters.Clear();
                    myCommand.Parameters.AddWithValue("@OPERATION", "INSERT");
                    myCommand.Parameters.AddWithValue("@SCHOOL_ID", intSchoolID);
                    myCommand.Parameters.AddWithValue("@CLASS_ID", -1);
                    myCommand.Parameters.AddWithValue("@ROLE_ID", intRoleID);
                    myCommand.Parameters.AddWithValue("@NUM_OF_USER", intNumOfUserSchool);
                    myCommand.Parameters.AddWithValue("@REGISTRATION_KEY", GetVoucherNumber(6));
                    myConnection.Open();
                    myCommand.ExecuteNonQuery();
                    myCommand.Dispose();
                    myConnection.Close();
                }
            }
            catch (SqlException ex)
            {
                strTextLine = strError + "SQL Error in GetSchoolID: " + ex.Message;
                Console.WriteLine(strTextLine);
                fileLog.WriteLine(strTextLine);
            }
            finally
            {
                myConnection.Close();
            }

            return intSchoolID;
        }

        static int GetClassID(int intRowNum, string[] arrRow, int intSchoolID, int intTeacherID)
        {
            int intClassID = -1;
            string strClassName = arrRow[14];
            string strGrade = "";
            byte intStatus = 1;
            int intRoleID = 5;

            SqlConnection myConnection = new SqlConnection(strSQLConnectionSring);
            try
            {
                string strQuery = @"
                    select ID 
                    from ILA_CLASS 
                    where SCHOOL_ID = @SchoolID 
                    and TEACHER_ID = @TeacherID 
                    and CLASS_NAME = @ClassName
                    ";
                //Console.WriteLine("GetClassID strQuery1( " + strQuery + ")";
                //Console.WriteLine("intSchoolID(" + intSchoolID.ToString() + ")";
                //Console.WriteLine("intTeacherID(" + intTeacherID.ToString() + ")";
                //Console.WriteLine("strClassName(" + strClassName + ")");
                myConnection.Open();
                SqlCommand myCommand = new SqlCommand(strQuery, myConnection);
                myCommand.Parameters.Clear();
                myCommand.Parameters.AddWithValue("@SchoolID", intSchoolID);
                myCommand.Parameters.AddWithValue("@TeacherID", intTeacherID);
                myCommand.Parameters.AddWithValue("@ClassName", strClassName);
                SqlDataReader myReader = myCommand.ExecuteReader();
                while (myReader.Read())
                {
                    intClassID = (int)myReader["ID"];
                    strTextLine = "Class already exists: " + strClassName + " (ID: " + intClassID + ")";
                    fileLog.WriteLine(strTextLine);
                }
                myReader.Dispose();
                myCommand.Dispose();
                myConnection.Close();

                if (intClassID == -1)
                {
                    strQuery = @" 
                        insert into ILA_CLASS (
                        SCHOOL_ID,
                        TEACHER_ID,
                        CLASS_NAME,
                        GRADE,
                        STATUS
                        ) values (
                        @SCHOOL_ID,
                        @TEACHER_ID,
                        @CLASS_NAME,
                        @GRADE,
                        @STATUS
                        );SELECT SCOPE_IDENTITY();";
                    myConnection.Open();
                    myCommand = new SqlCommand(strQuery, myConnection);
                    myCommand.Parameters.Clear();
                    myCommand.Parameters.AddWithValue("@SCHOOL_ID", intSchoolID);
                    myCommand.Parameters.AddWithValue("@TEACHER_ID", intTeacherID);
                    myCommand.Parameters.AddWithValue("@CLASS_NAME", strClassName);
                    myCommand.Parameters.AddWithValue("@GRADE", strGrade);
                    myCommand.Parameters.AddWithValue("@STATUS", intStatus);
                    intClassID = (int)(decimal)myCommand.ExecuteScalar();
                    myReader.Dispose();
                    myCommand.Dispose();
                    myConnection.Close();

                    strTextLine = "Class Added: " + strClassName;
                    fileLog.WriteLine(strTextLine);
                    fileLogDistrict.WriteLine(strTextLine);
                    intAddedClasses++;

                    // ILA_SCHOOL_LIC_INFO
                    strQuery = @"ILA_UID_SCHOOL_LIC_INFO";
                    myCommand = new SqlCommand(strQuery, myConnection);
                    myCommand.CommandType = CommandType.StoredProcedure;
                    myCommand.Parameters.Clear();
                    myCommand.Parameters.AddWithValue("@OPERATION", "INSERT");
                    myCommand.Parameters.AddWithValue("@SCHOOL_ID", intSchoolID);
                    myCommand.Parameters.AddWithValue("@CLASS_ID", intClassID);
                    myCommand.Parameters.AddWithValue("@ROLE_ID", intRoleID);
                    myCommand.Parameters.AddWithValue("@NUM_OF_USER", intNumOfUserClass);
                    myCommand.Parameters.AddWithValue("@REGISTRATION_KEY", GetVoucherNumber(6));
                    myConnection.Open();
                    myCommand.ExecuteNonQuery();
                    myCommand.Dispose();
                    myConnection.Close();
                }
            }
            catch (SqlException ex)
            {
                strTextLine = strError + "SQL Error in GetClassID: " + ex.Message;
                Console.WriteLine(strTextLine);
                fileLog.WriteLine(strTextLine);
            }
            finally
            {
                myConnection.Close();
            }

            return intClassID;
        }

        static int GetUser(int intRowNum, string[] arrRow, int intDistrictID, string strUserType)
        {
            int intUserID = -1;
            int AffiliateId = 0;
            string FirstName = "";
            string LastName = "";
            string Email = "";
            string UserName = "";
            string Password = "";
            byte IsSuperUser = 0;
            byte UpdatePassword = 0;
            byte Authorised = 1;
            string PasswordSalt = "9LOIxqejU+JkI9XtJ0EzEg==";
            string PasswordQuestion = "";
            string PasswordAnswer = "";
            byte IsApproved = 1;
            DateTime CurrentTimeUtc = DateTime.UtcNow.Date;
            DateTime CreateDate = DateTime.Today;
            int UniqueEmail = 0;
            int PasswordFormat = 0;
            Guid UserId = Guid.NewGuid();

            switch (strUserType)
            {
                case "Teacher":
                    FirstName = arrRow[16];
                    LastName = arrRow[17];
                    Email = arrRow[18];
                    UserName = arrRow[19];
                    Password = arrRow[20];
                    break;
                case "Student":
                    FirstName = arrRow[22];
                    LastName = arrRow[23];
                    Email = arrRow[24];
                    UserName = arrRow[25];
                    Password = arrRow[26];
                    break;
                default:
                    break;
            }
            string DisplayName = FirstName + " " + LastName;

            SqlConnection myConnection = new SqlConnection(strSQLConnectionSring);
            try
            {
                intUserID = GetUserID(intRowNum, UserName, strUserType, intDistrictID, false);
                if (intUserID == -1) 
                {
                    if (Password.Length < constPasswordLengthRequired)
                    {
                        strTextLine = strError + "Invalid password length " + Password.Length.ToString();
                        fileLog.WriteLine(strTextLine);
                        return intUserID;
                    }

                    // pd_Users & pd_UserPortals
                    string strQuery = @"pd_AddUser";
                    SqlCommand myCommand = new SqlCommand(strQuery, myConnection);
                    myCommand.CommandType = CommandType.StoredProcedure;
                    myCommand.Parameters.Clear();
                    myCommand.Parameters.AddWithValue("@PortalID", intDistrictID);
                    myCommand.Parameters.AddWithValue("@UserName", UserName);
                    myCommand.Parameters.AddWithValue("@FirstName", FirstName);
                    myCommand.Parameters.AddWithValue("@LastName", LastName);
                    myCommand.Parameters.AddWithValue("@AffiliateId", AffiliateId);
                    myCommand.Parameters.AddWithValue("@IsSuperUser", IsSuperUser);
                    myCommand.Parameters.AddWithValue("@Email", Email);
                    myCommand.Parameters.AddWithValue("@DisplayName", DisplayName);
                    myCommand.Parameters.AddWithValue("@UpdatePassword", UpdatePassword);
                    myCommand.Parameters.AddWithValue("@Authorised", Authorised);
                    myCommand.Parameters.AddWithValue("@CreatedByUserID", intCreatedByUserID);
                    //myCommand.Parameters.Add("Return", SqlDbType.Int).Direction = ParameterDirection.ReturnValue;

                    myConnection.Open();
                    myCommand.ExecuteNonQuery();

                    // Not working??
                    //intUserID = (int)myCommand.Parameters["@UserId"].Value;
                    //Console.WriteLine("intUserID(" + intUserID + ")");

                    myCommand.Dispose();
                    myConnection.Close();

                    switch (strUserType)
                    {
                        case "Teacher":
                            intAddedTeachers++;
                            break;
                        case "Student":
                            intAddedStudents++;
                            break;
                        default:
                            break;
                    }

                    // Oh well for now as the sproc does not return the ID.
                    intUserID = GetUserID(intRowNum, UserName, strUserType, intDistrictID, true);

                    if (intUserID == -1)
                    {
                        strTextLine = strError + "insert pd_Users: " + UserName;
                        fileLog.WriteLine(strTextLine);
                    }

                    // aspnet_Membership & aspnet_Users
                    strQuery = @"aspnet_Membership_CreateUser";
                    myCommand = new SqlCommand(strQuery, myConnection);
                    myCommand.CommandType = CommandType.StoredProcedure;
                    myCommand.Parameters.Clear();
                    myCommand.Parameters.AddWithValue("@ApplicationName", strApplicationName);
                    myCommand.Parameters.AddWithValue("@UserName", UserName);
                    myCommand.Parameters.AddWithValue("@Password", Password);
                    myCommand.Parameters.AddWithValue("@PasswordSalt", PasswordSalt);
                    myCommand.Parameters.AddWithValue("@Email", Email);
                    myCommand.Parameters.AddWithValue("@PasswordQuestion", PasswordQuestion);
                    myCommand.Parameters.AddWithValue("@PasswordAnswer", PasswordAnswer);
                    myCommand.Parameters.AddWithValue("@IsApproved", IsApproved);
                    myCommand.Parameters.AddWithValue("@CurrentTimeUtc", CurrentTimeUtc);
                    myCommand.Parameters.AddWithValue("@CreateDate", CreateDate);
                    myCommand.Parameters.AddWithValue("@UniqueEmail", UniqueEmail);
                    myCommand.Parameters.AddWithValue("@PasswordFormat", PasswordFormat);
                    myCommand.Parameters.AddWithValue("@UserId", UserId).Direction = ParameterDirection.Output;
                    myConnection.Open();
                    SqlDataReader myReader = myCommand.ExecuteReader();

                    string strUserId = myCommand.Parameters["@UserId"].Value.ToString();
                    //Console.WriteLine("strUserId(" + strUserId + ")");
                    if (strUserId == "")
                    {
                        intUserID = -1;

                        strTextLine = strError + "insert aspnet_Membership: " + UserName;
                        fileLog.WriteLine(strTextLine);
                    }
                    else
                    {
                        strTextLine = strUserType + " Added: " + DisplayName + ", Email: " + Email + ", UserName: " + UserName;
                        fileLog.WriteLine(strTextLine);
                        fileLogDistrict.WriteLine(strTextLine);

                        booNewUserAdded = true;
                    }
                    myReader.Dispose();
                    myCommand.Dispose();
                    myConnection.Close();
                }
            }
            catch (SqlException ex)
            {
                strTextLine = strError + "SQL Error in GetUser: " + ex.Message;
                Console.WriteLine(strTextLine);
                fileLog.WriteLine(strTextLine);
            }
            finally
            {
                myConnection.Close();
            }

            return intUserID;
        }

        static int GetUserID(int intRowNum, string strUserName, string strUserType, int intDistrictID, bool booVerify)
        {
            int intUserID = -1;

            SqlConnection myConnection = new SqlConnection(strSQLConnectionSring);
            try
            {
                // pd_Users <--->> pd_UserPortals

                // Is the username already in this district?
                string strQuery = @"
                    select u.userid 
                    from pd_users u 
                    inner join pd_UserPortals p on u.userid = p.userid 
                    where u.username = @UserName
                    and p.portalid = @PortalID
                    ";
                //Console.WriteLine("strQuery(" + strQuery + ")");
                myConnection.Open();
                SqlCommand myCommand = new SqlCommand(strQuery, myConnection);
                myCommand.Parameters.Clear();
                myCommand.Parameters.AddWithValue("@UserName", strUserName);
                myCommand.Parameters.AddWithValue("@PortalID", intDistrictID);
                SqlDataReader myReader = myCommand.ExecuteReader();
                while (myReader.Read())
                {
                    intUserID = (int)myReader["userid"];

                    if (!booVerify)
                    {
                        strTextLine = "Username already exists in this district:  " + strUserName + " (ID: " + intUserID + ")";
                        fileLog.WriteLine(strTextLine);
                        strTextLine = strUserType + " with the username already exists:  " + strUserName;
                        fileLogDistrict.WriteLine(strTextLine);
                    }
                }
                myReader.Dispose();
                myCommand.Dispose();
                myConnection.Close();

                // Is the username already in another district
                strQuery = @"
                    select u.userid 
                    from pd_users u 
                    inner join pd_UserPortals p on u.userid = p.userid 
                    where u.username = @UserName
                    and p.portalid <> @PortalID
                    ";
                //Console.WriteLine("strQuery(" + strQuery + ")");
                myConnection.Open();
                myCommand = new SqlCommand(strQuery, myConnection);
                myCommand.Parameters.Clear();
                myCommand.Parameters.AddWithValue("@UserName", strUserName);
                myCommand.Parameters.AddWithValue("@PortalID", intDistrictID);
                myReader = myCommand.ExecuteReader();
                while (myReader.Read())
                {
                    intUserID = -2;

                    strTextLine = "Username already exists in another district:  " + strUserName + " (ID: " + intUserID + ")";
                    fileLog.WriteLine(strTextLine);
                }
                myReader.Dispose();
                myCommand.Dispose();
                myConnection.Close();
            }
            catch (SqlException ex)
            {
                strTextLine = strError + "SQL Error in GetUserID: " + ex.Message;
                Console.WriteLine(strTextLine);
                fileLog.WriteLine(strTextLine);
            }
            finally
            {
                myConnection.Close();
            }

            /*
             * -1: username not found
             * -2: username found in another district
             * else: username found in this district
             */
            return intUserID;
        }

        static int AddUserRole(int intRowNum, int intUserID, string strUserType, int intDistrictID, int intSchoolID, int intClassID)
        {
            int intUserRoleID = -1;
            //int intProfileID = -1;

            SqlConnection myConnection = new SqlConnection(strSQLConnectionSring);
            try
            {
                // pd_UserRoles, pd_UserProfile, ILA_CLASS_STUDENT
                string strQuery = @"ILA_ADD_ROLE_FOR_USER";
                SqlCommand myCommand = new SqlCommand(strQuery, myConnection);
                myCommand.CommandType = CommandType.StoredProcedure;
                myCommand.Parameters.Clear();
                myCommand.Parameters.AddWithValue("@USER_ID", intUserID);
                myCommand.Parameters.AddWithValue("@ROLE_Name", strUserType.ToUpper());
                myCommand.Parameters.AddWithValue("@CREATEDBYUSERID", intCreatedByUserID);
                myCommand.Parameters.AddWithValue("@SCHOOL_ID", intSchoolID);
                myCommand.Parameters.AddWithValue("@CLASS_ID", intClassID);
                myCommand.Parameters.AddWithValue("@PORTAL_ID", intDistrictID);
                myConnection.Open();
                myCommand.ExecuteNonQuery();
                myCommand.Dispose();
                myConnection.Close();

                strQuery = @"
                    select userroleid 
                    from pd_userroles 
                    where userid = @UserID
                    ";
                //Console.WriteLine("strQuery(" + strQuery + ")");
                myConnection.Open();
                myCommand = new SqlCommand(strQuery, myConnection);
                myCommand.Parameters.Clear();
                myCommand.Parameters.AddWithValue("@UserID", intUserID);
                SqlDataReader myReader = myCommand.ExecuteReader();
                while (myReader.Read())
                {
                    intUserRoleID = (int)myReader["userroleid"];
                }
                myReader.Dispose();
                myCommand.Dispose();
                myConnection.Close();

                if (intUserRoleID == -1)
                {
                    strTextLine = strError + "insert pd_UserRoles: " + intUserID;
                    Console.WriteLine(strTextLine);
                    fileLog.WriteLine(strTextLine);
                }
            }
            catch (SqlException ex)
            {
                strTextLine = strError + "SQL Error in AddUserRole: " + ex.Message;
                Console.WriteLine(strTextLine);
                fileLog.WriteLine(strTextLine);
            }
            finally
            {
                myConnection.Close();
            }

            return intUserRoleID;
        }

        static int UpdateSchoolClassLicense(int intRowNum, int intUserID, string strUserType, int intDistrictID, int intSchoolID, int intClassID)
        {
            int intResult = -1;
            int intILA_SCHOOL_LIC_INFO_ID = -1;
            int intAvail = -1;
            int intAvailUpdated = -1;

            SqlConnection myConnection = new SqlConnection(strSQLConnectionSring);

            try
            {
                // I don't know why this increments the avail instead of decrements: 
                //  ILA_ASSIGN_TO_CLASS 'STUDENT_ASSIGN',1503,'32582','',647
                //
                //string strQuery = @"ILA_ASSIGN_TO_CLASS";
                //SqlCommand myCommand = new SqlCommand(strQuery, myConnection);
                //myCommand.CommandType = CommandType.StoredProcedure;
                //myCommand.Parameters.Clear();
                //myCommand.Parameters.AddWithValue("@OPERATION", "STUDENT_ASSIGN");
                //myCommand.Parameters.AddWithValue("@ASSIGN_ID", intClassID);
                //myCommand.Parameters.AddWithValue("@ASSIGNEE_ID", intUserID);
                //myCommand.Parameters.AddWithValue("@LICENSE_INFO", "");
                //myCommand.Parameters.AddWithValue("@PORTAL_ID", intDistrictID);
                //Console.Write("(" + strQuery + ")(" + "STUDENT_ASSIGN" + ")(" + intClassID.ToString() + ")(" + intUserID.ToString() + ")(" + "" + ")(" + intDistrictID.ToString() + ")");

                string strQuery1 = @"
                    select 
                    ID,
                    AVAILABLE_USER_LIMIT 
                    from ILA_SCHOOL_LIC_INFO 
                    WHERE SCHOOL_ID = " + intSchoolID + " ";

                string strQuery2 = @" 
                    UPDATE ILA_SCHOOL_LIC_INFO SET 
                    AVAILABLE_USER_LIMIT = AVAILABLE_USER_LIMIT - 1
                    WHERE SCHOOL_ID = " + intSchoolID + " ";

                if (strUserType.ToUpper() == "STUDENT")
                {
                    strQuery1 += "AND CLASS_ID = " + intClassID + " ";
                    strQuery2 += "AND CLASS_ID = " + intClassID + " ";
                }
                else
                {
                    strQuery1 += "AND CLASS_ID = -1 ";
                    strQuery2 += "AND CLASS_ID = -1 ";
                }

                myConnection.Open();
                SqlCommand myCommand = new SqlCommand(strQuery1, myConnection);
                SqlDataReader myReader = myCommand.ExecuteReader();
                while (myReader.Read())
                {
                    intILA_SCHOOL_LIC_INFO_ID = (int)myReader["ID"];
                    intAvail = (int)myReader["AVAILABLE_USER_LIMIT"];
                }
                myReader.Dispose();
                myCommand.Dispose();
                myConnection.Close();

                myConnection.Open();
                myCommand = new SqlCommand(strQuery2, myConnection);
                myCommand.ExecuteScalar();
                intResult = 1;
                myCommand.Dispose();
                myConnection.Close();

                myConnection.Open();
                myCommand = new SqlCommand(strQuery1, myConnection);
                myReader = myCommand.ExecuteReader();
                while (myReader.Read())
                {
                    intAvailUpdated = (int)myReader["AVAILABLE_USER_LIMIT"];
                }
                myReader.Dispose();
                myCommand.Dispose();
                myConnection.Close();

                strTextLine = "ILA_SCHOOL_LIC_INFO Updated: ID(" + intILA_SCHOOL_LIC_INFO_ID.ToString() + ") Avail(" + intAvail.ToString() + ") -> (" + intAvailUpdated.ToString() + ")";
                fileLog.WriteLine(strTextLine);

            }
            catch (SqlException ex)
            {
                strTextLine = strError + "SQL Error in UpdateSchoolClassLicense: " + ex.Message;
                Console.WriteLine(strTextLine);
                fileLog.WriteLine(strTextLine);
            }
            finally
            {
                myConnection.Close();
            }

            return intResult;
        }

        static bool SendMail(string strFrom, string strTo, string strSubject, string strBody)
        {
            SmtpClient client = new SmtpClient()
            {
                Host = constMail_Host,
                Port = constMail_Port,
                EnableSsl = true,
                DeliveryMethod = SmtpDeliveryMethod.Network,
                UseDefaultCredentials = false,
                Credentials = new NetworkCredential(constMail_SMTPUser, constMail_SMTPPass)
            };

            try
            {
                if (strFrom == "")
                {
                    strFrom = constMail_From;
                }
                if (strSubject == "")
                {
                    strSubject = constMail_Subject;
                }

                MailMessage message = new MailMessage(strFrom, strTo)
                {
                    Subject = strSubject,
                    Body = strBody
                };

                client.Send(message);
            }
            catch (Exception ex)
            {
                strTextLine = strError + "in SendMail: " + ex.Message;
                strTextLine += "\r\n\r\n From(" + strFrom + ")";
                strTextLine += "\r\n\r\n To(" + strTo + ")";
                strTextLine += "\r\n\r\n Subject(" + strSubject + ")";
                strTextLine += "\r\n\r\n Body(" + strBody + ")";
                Console.WriteLine(strTextLine);
                //fileLog.WriteLine(strTextLine);
            }

            return true;
        }

        static int DownloadFile()
        {
            Sftp sftp = new Sftp(constSFTPHost, constSFTPUser, constSFTPPass);

            try
            {
                sftp.Connect();
            }
            catch (Exception ex)
            {
                strTextLine = strError + "Connecting in DownloadFile: " + ex.Message;
                //Console.WriteLine(strTextLine);
                fileLog.WriteLine(strTextLine);
                return 0;
            }

            try
            {
                sftp.Get(constSFTPPath + strFileName, ".");
            }
            catch (Exception ex)
            {
                return 2;
            }

            try
            {
                sftp.Delete(constSFTPPath + strFileName);
            }
            catch (Exception ex)
            {
                strTextLine = strError + "Deleting in DownloadFile: " + ex.Message;
                //Console.WriteLine(strTextLine);
                fileLog.WriteLine(strTextLine);
                return 0;
            }

            sftp.Close();

            return 1;
        }

        private static Random random = new Random();

        public static string GetVoucherNumber(int length)
        {
            var chars = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
            var result = new string(
                Enumerable.Repeat(chars, length)
                          .Select(s => s[random.Next(s.Length)])
                          .ToArray());

            return result;
        }
    }
}

using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;
using System.Net.Mail;
using System.Net;

namespace Mail_Send
{
    class Program
    {
        static void Main(string[] args)
        {
            //string strHost = "mail.mydomain.com";
            //int strPort = 25;
            //string strSMTPUser = "mailsender@mydomain.com";
            //string strSMTPPass = "ms1234!";

            string strHost = "smtp.gmail.com";
            int strPort = 587;
            string strSMTPUser = "webmaster@mydomain.com";
            string strSMTPPass = "*****";

            //string strHost = "ses-smtp-user.20150430-163732";
            //int strPort = 25;
            //string strSMTPUser = "AKIAI5RD4YHOTZIO27LQ";
            //string strSMTPPass = "AtrsWrBHD3ltv9rmSIaMySnC4MrbslkxO0y2BYIexHFW";

            string strFrom = "webmaster@mydomain.com";
            string strTo = "eyi@mydomain.com";
            string strSubject = "Test ";
            string strBody = "This is a test";

            strSubject += string.Format("{0:HH:mm:ss tt}", DateTime.Now);

            SmtpClient client = new SmtpClient()
            {
                Host = strHost,
                Port = strPort,
                EnableSsl = true,
                DeliveryMethod = SmtpDeliveryMethod.Network,
                UseDefaultCredentials = false,
                Credentials = new NetworkCredential(strSMTPUser, strSMTPPass)
            };

            try
            {
                MailMessage message = new MailMessage(strFrom, strTo)
                {
                    Subject = strSubject,
                    Body = strBody
                };

                client.Send(message);
            }
            catch (Exception ex)
            {
                Console.WriteLine("There was an error!" + ex.Message);
            }

            Console.WriteLine("Press any key to exit.");
            Console.ReadKey();
        }
    }
}

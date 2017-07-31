<!--#include file="md5.asp"-->

<%-- This is an ASP page run on (legacy) IIS of a LDAP server.  Running it on the LDAP server itself probably does not require security certificate.  Have a routine send a POST request to http://<address>/ldap/ldap.asp and wait for an answer. --%>

<%
'for each x in Request.ServerVariables
'	response.write(x & ":" & Request.ServerVariables(x) & "<br />")
'next
  
strMsg = ""
  
If Request.Form("username") <> "" And Request.Form("password") <> "" Then
	strUserName = Request.Form("username")
	strNewPassword = Request.Form("password")
	strS = Request.Form("s")

	' TESTING
	'strUserName = "etest"
	'strNewPassword = "password"

	strHash = "vjvVTYVkchkg578%4vjvVTYVkchkg578%4vjvVTYVkchkg578%4"
	strHash = md5(strUserName & strHash & strNewPassword)
	'response.write strHash
	if (strS = strHash) then
	
		Set oNetwork = CreateObject("WScript.Network")
		sADSPath= "fandm/" & strUserName
		Set objUser = GetObject("WinNT://" & sADSPath & ",user")
		' strFullName = objUser.FullName
		' strMsg = strMsg & "FullName: " & strFullName & "<br />"

		' Set objUser = GetObject ("LDAP://CN=" & strFullName & ",OU=Users,OU=ForceandMotion,DC=fandm,DC=local")
		objUser.SetPassword strNewPassword
		strMsg = strMsg & "Password Changed to " & strNewPassword & "<br />"
		
		response.write strMsg
	end if
End If
%>
<!--html>
<head>
	<title></title>
</head>
<body>
<form action="<%=Request.ServerVariables("SCRIPT_NAME")%>" method="post">
	User Name: <input type="text" name="username" value="<%=strUserName%>" />
	New Password: <input type="text" name="password" value="<%=strNewPassword%>" />
	<input type="submit" value="submit" />
</form>
<%=strMsg%>
</body>
</html-->
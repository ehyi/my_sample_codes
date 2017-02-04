VERSION 5.00
Begin VB.Form Form1 
   BorderStyle     =   1  'Fixed Single
   Caption         =   "SUSHI"
   ClientHeight    =   6795
   ClientLeft      =   60
   ClientTop       =   345
   ClientWidth     =   9480
   LinkTopic       =   "Form1"
   MaxButton       =   0   'False
   MinButton       =   0   'False
   ScaleHeight     =   453
   ScaleMode       =   3  'Pixel
   ScaleWidth      =   632
   StartUpPosition =   2  'CenterScreen
   Begin VB.PictureBox Picture3 
      BackColor       =   &H00C0C0C0&
      Enabled         =   0   'False
      Height          =   495
      Left            =   8160
      Picture         =   "sushi.frx":0000
      ScaleHeight     =   435
      ScaleWidth      =   1275
      TabIndex        =   28
      TabStop         =   0   'False
      Top             =   0
      Width           =   1335
   End
   Begin VB.TextBox txtTotalOverdue 
      Alignment       =   1  'Right Justify
      Height          =   285
      Left            =   7080
      Locked          =   -1  'True
      TabIndex        =   13
      Text            =   "0"
      Top             =   5280
      Width           =   1095
   End
   Begin VB.TextBox txtTotalRegistered 
      Alignment       =   1  'Right Justify
      Height          =   285
      Left            =   7080
      Locked          =   -1  'True
      TabIndex        =   12
      Text            =   "0"
      Top             =   4920
      Width           =   1095
   End
   Begin VB.PictureBox Picture2 
      Appearance      =   0  'Flat
      BackColor       =   &H80000005&
      BorderStyle     =   0  'None
      Enabled         =   0   'False
      ForeColor       =   &H80000008&
      Height          =   735
      Left            =   5880
      Picture         =   "sushi.frx":2142
      ScaleHeight     =   735
      ScaleWidth      =   3615
      TabIndex        =   7
      Top             =   5760
      Width           =   3615
   End
   Begin VB.PictureBox Picture1 
      BackColor       =   &H00C0C0C0&
      Enabled         =   0   'False
      Height          =   495
      Left            =   0
      Picture         =   "sushi.frx":4699
      ScaleHeight     =   435
      ScaleWidth      =   1275
      TabIndex        =   6
      TabStop         =   0   'False
      Top             =   0
      Width           =   1335
   End
   Begin VB.CommandButton cmdClear 
      Caption         =   "Clear &All"
      Height          =   375
      Left            =   8400
      TabIndex        =   1
      Top             =   4800
      Width           =   975
   End
   Begin VB.CommandButton cmdClose 
      Caption         =   "&Close"
      Height          =   375
      Left            =   8400
      TabIndex        =   2
      Top             =   5280
      Width           =   975
   End
   Begin VB.TextBox txtInput 
      BackColor       =   &H00C0C0FF&
      Height          =   285
      Left            =   0
      TabIndex        =   0
      Top             =   480
      Width           =   510
   End
   Begin VB.TextBox txtStatus 
      BeginProperty Font 
         Name            =   "MS Sans Serif"
         Size            =   13.5
         Charset         =   0
         Weight          =   400
         Underline       =   0   'False
         Italic          =   0   'False
         Strikethrough   =   0   'False
      EndProperty
      Height          =   5655
      Left            =   240
      Locked          =   -1  'True
      MultiLine       =   -1  'True
      ScrollBars      =   2  'Vertical
      TabIndex        =   3
      TabStop         =   0   'False
      Top             =   840
      Width           =   5655
   End
   Begin VB.Label Label4 
      Caption         =   "Scanned Time: "
      Height          =   255
      Left            =   6000
      TabIndex        =   31
      Top             =   600
      Width           =   1095
   End
   Begin VB.Label lblColor 
      BackColor       =   &H00C0C0C0&
      BorderStyle     =   1  'Fixed Single
      Height          =   255
      Index           =   14
      Left            =   0
      TabIndex        =   30
      Top             =   5880
      Width           =   255
   End
   Begin VB.Label lblColor 
      BackColor       =   &H00C0C0C0&
      BorderStyle     =   1  'Fixed Single
      Height          =   255
      Index           =   13
      Left            =   0
      TabIndex        =   29
      Top             =   5520
      Width           =   255
   End
   Begin VB.Label lblColor 
      BackColor       =   &H00C0C0C0&
      BorderStyle     =   1  'Fixed Single
      Height          =   255
      Index           =   12
      Left            =   0
      TabIndex        =   27
      Top             =   5160
      Width           =   255
   End
   Begin VB.Label lblColor 
      BackColor       =   &H00C0C0C0&
      BorderStyle     =   1  'Fixed Single
      Height          =   255
      Index           =   11
      Left            =   0
      TabIndex        =   26
      Top             =   4800
      Width           =   255
   End
   Begin VB.Label lblColor 
      BackColor       =   &H00C0C0C0&
      BorderStyle     =   1  'Fixed Single
      Height          =   255
      Index           =   10
      Left            =   0
      TabIndex        =   25
      Top             =   4440
      Width           =   255
   End
   Begin VB.Label lblColor 
      BackColor       =   &H00C0C0C0&
      BorderStyle     =   1  'Fixed Single
      Height          =   255
      Index           =   9
      Left            =   0
      TabIndex        =   24
      Top             =   4080
      Width           =   255
   End
   Begin VB.Label lblColor 
      BackColor       =   &H00C0C0C0&
      BorderStyle     =   1  'Fixed Single
      Height          =   255
      Index           =   8
      Left            =   0
      TabIndex        =   23
      Top             =   3720
      Width           =   255
   End
   Begin VB.Label lblColor 
      BackColor       =   &H00C0C0C0&
      BorderStyle     =   1  'Fixed Single
      Height          =   255
      Index           =   7
      Left            =   0
      TabIndex        =   22
      Top             =   3360
      Width           =   255
   End
   Begin VB.Label lblColor 
      BackColor       =   &H00C0C0C0&
      BorderStyle     =   1  'Fixed Single
      Height          =   255
      Index           =   6
      Left            =   0
      TabIndex        =   21
      Top             =   3000
      Width           =   255
   End
   Begin VB.Label lblColor 
      BackColor       =   &H00C0C0C0&
      BorderStyle     =   1  'Fixed Single
      Height          =   255
      Index           =   5
      Left            =   0
      TabIndex        =   20
      Top             =   2640
      Width           =   255
   End
   Begin VB.Label lblColor 
      BackColor       =   &H00C0C0C0&
      BorderStyle     =   1  'Fixed Single
      Height          =   255
      Index           =   4
      Left            =   0
      TabIndex        =   19
      Top             =   2280
      Width           =   255
   End
   Begin VB.Label lblColor 
      BackColor       =   &H00C0C0C0&
      BorderStyle     =   1  'Fixed Single
      Height          =   255
      Index           =   3
      Left            =   0
      TabIndex        =   18
      Top             =   1920
      Width           =   255
   End
   Begin VB.Label lblColor 
      BackColor       =   &H00C0C0C0&
      BorderStyle     =   1  'Fixed Single
      Height          =   255
      Index           =   2
      Left            =   0
      TabIndex        =   17
      Top             =   1560
      Width           =   255
   End
   Begin VB.Label lblColor 
      BackColor       =   &H00C0C0C0&
      BorderStyle     =   1  'Fixed Single
      Height          =   255
      Index           =   1
      Left            =   0
      TabIndex        =   16
      Top             =   1200
      Width           =   255
   End
   Begin VB.Label lblColor 
      BackColor       =   &H00C0C0C0&
      BorderStyle     =   1  'Fixed Single
      Height          =   255
      Index           =   0
      Left            =   0
      TabIndex        =   15
      Top             =   840
      Width           =   255
   End
   Begin VB.Line Line1 
      BorderColor     =   &H00E0E0E0&
      Index           =   1
      X1              =   552
      X2              =   552
      Y1              =   296
      Y2              =   376
   End
   Begin VB.Line Line2 
      BorderColor     =   &H00E0E0E0&
      Index           =   2
      X1              =   400
      X2              =   552
      Y1              =   376
      Y2              =   376
   End
   Begin VB.Label lblDateTime 
      Height          =   255
      Left            =   7200
      TabIndex        =   14
      Top             =   600
      Width           =   2175
   End
   Begin VB.Line Line2 
      BorderColor     =   &H00E0E0E0&
      Index           =   0
      X1              =   400
      X2              =   552
      Y1              =   296
      Y2              =   296
   End
   Begin VB.Line Line1 
      BorderColor     =   &H00E0E0E0&
      Index           =   0
      X1              =   400
      X2              =   400
      Y1              =   296
      Y2              =   376
   End
   Begin VB.Label Label3 
      Caption         =   "Overdue:"
      Height          =   255
      Left            =   6120
      TabIndex        =   11
      Top             =   5280
      Width           =   975
   End
   Begin VB.Label Label2 
      Caption         =   "Registered:"
      Height          =   255
      Left            =   6120
      TabIndex        =   10
      Top             =   4920
      Width           =   975
   End
   Begin VB.Label Label1 
      Alignment       =   2  'Center
      BorderStyle     =   1  'Fixed Single
      Caption         =   "< TOTALS >"
      BeginProperty Font 
         Name            =   "MS Sans Serif"
         Size            =   8.25
         Charset         =   0
         Weight          =   700
         Underline       =   0   'False
         Italic          =   0   'False
         Strikethrough   =   0   'False
      EndProperty
      ForeColor       =   &H00800080&
      Height          =   255
      Left            =   6120
      TabIndex        =   9
      Top             =   4560
      Width           =   2055
   End
   Begin VB.Label lblScore 
      Alignment       =   2  'Center
      BackColor       =   &H0000FF00&
      BeginProperty Font 
         Name            =   "MS Sans Serif"
         Size            =   18
         Charset         =   0
         Weight          =   700
         Underline       =   0   'False
         Italic          =   0   'False
         Strikethrough   =   0   'False
      EndProperty
      ForeColor       =   &H00000000&
      Height          =   3375
      Left            =   6000
      TabIndex        =   8
      Top             =   960
      Width           =   3375
   End
   Begin VB.Label lblConfig 
      Alignment       =   2  'Center
      ForeColor       =   &H00404040&
      Height          =   255
      Left            =   0
      TabIndex        =   5
      Top             =   6600
      Width           =   9495
   End
   Begin VB.Label lblStoreName 
      Alignment       =   2  'Center
      BorderStyle     =   1  'Fixed Single
      BeginProperty Font 
         Name            =   "Arial"
         Size            =   20.25
         Charset         =   0
         Weight          =   700
         Underline       =   0   'False
         Italic          =   0   'False
         Strikethrough   =   0   'False
      EndProperty
      ForeColor       =   &H000040C0&
      Height          =   495
      Left            =   1560
      TabIndex        =   4
      Top             =   0
      Width           =   6375
   End
End
Attribute VB_Name = "Form1"
Attribute VB_GlobalNameSpace = False
Attribute VB_Creatable = False
Attribute VB_PredeclaredId = True
Attribute VB_Exposed = False
Dim cn
Dim intBarcodeLength, strTripTime, intA, intB, intC, intD, intE, intF
Dim strAColor, strBColor, strCColor, strDColor, strEColor, strFColor, strGColor, strHColor, strIColor, strJColor
Dim strBarCode
Dim intLblColorIndex, booValidInput

Private Declare Function sndPlaySound Lib "winmm" Alias "sndPlaySoundA" (ByVal lpszSoundName As String, ByVal uFlags As Long) As Long
Public Enum SND_Settings
    SND_SYNC = &H0
    SND_ASYNC = &H1
    SND_NODEFAULT = &H2
    SND_MEMORY = &H4
    SND_LOOP = &H8
    SND_NOSTOP = &H10
    SW_SHOW = 5
End Enum

Const INTLBLCOLORINDEXMAX = 14      ' Number of lblColor
    
Public Sub Play(fname As String, Optional Settings As SND_Settings = SND_ASYNC)
    Dim retval As Long
    retval = sndPlaySound(fname, Settings)
End Sub

Private Sub cmdClear_Click()
    answer = MsgBox("Are you sure?", vbYesNo, "Clear All")
    If answer = vbYes Then
        Set rsDelete = CreateObject("ADODB.Recordset")
        strCmdDelete = "delete from sushi "
        'Debug.Print "strCmdDelete(" & strCmdDelete & ")"
        rsDelete.Open strCmdDelete, cn
        
        Set rsUpdate = CreateObject("ADODB.Recordset")
        strCmdUpdate = "update Totals set TotalRegistered = 0, TotalOverDue = 0 "
        'Debug.Print "strCmdUpdate(" & strCmdUpdate & ")"
        rsUpdate.Open strCmdUpdate, cn
        
        txtStatus = ""
        lblScore = ""
        txtTotalRegistered = 0
        txtTotalOverdue = 0
        For i = 0 To INTLBLCOLORINDEXMAX
            lblColor(i).BackColor = "&HC0C0C0"
        Next
    End If
    txtInput.SetFocus
End Sub

Private Sub cmdClose_Click()
    Set rsUpdate = CreateObject("ADODB.Recordset")
    strCmdUpdate = "update Totals set TotalRegistered = " & CInt(txtTotalRegistered) & ", TotalOverDue = " & CInt(txtTotalOverdue)
    'Debug.Print "strCmdUpdate(" & strCmdUpdate & ")"
    rsUpdate.Open strCmdUpdate, cn

    cn.Close
    End
End Sub

Private Sub Form_Load()
    intLblColorIndex = 0
    
    Call ConnectDB
        
    Set rs = CreateObject("ADODB.Recordset")
    strCmd = "select * from TimeConfig"
    rs.Open strCmd, cn
    If Not rs.EOF Then
        intBarcodeLength = CInt(Trim(rs("BarcodeLength")))
        strTripTime = CInt(Trim(rs("TripTime")))
        intA = CInt(Trim(rs("A")))
        intB = CInt(Trim(rs("B")))
        intC = CInt(Trim(rs("C")))
        intD = CInt(Trim(rs("D")))
        intE = CInt(Trim(rs("E")))
        intF = CInt(Trim(rs("F")))
        intG = CInt(Trim(rs("G")))
        intH = CInt(Trim(rs("H")))
        intI = CInt(Trim(rs("I")))
        intJ = CInt(Trim(rs("J")))
        If Not IsNull(rs("AColor")) Then
            strAColor = rs("AColor")
        End If
        If Not IsNull(rs("BColor")) Then
            strBColor = rs("BColor")
        End If
        If Not IsNull(rs("CColor")) Then
            strCColor = rs("CColor")
        End If
        If Not IsNull(rs("DColor")) Then
            strDColor = rs("DColor")
        End If
        If Not IsNull(rs("EColor")) Then
            strEColor = rs("EColor")
        End If
        If Not IsNull(rs("FColor")) Then
            strFColor = rs("FColor")
        End If
        If Not IsNull(rs("GColor")) Then
            strGColor = rs("GColor")
        End If
        If Not IsNull(rs("HColor")) Then
            strHColor = rs("HColor")
        End If
        If Not IsNull(rs("IColor")) Then
            strIColor = rs("IColor")
        End If
        If Not IsNull(rs("JColor")) Then
            strJColor = rs("JColor")
        End If
        strStoreName = Trim(rs("StoreName"))
    End If
    rs.Close
    
    'lblStoreName = strStoreName
    lblStoreName = "S U S H I   Y A M A"
    lblConfig = "Total Kaiten Time (mins): " & strTripTime & "   " & "Plate Max Time (mins) " & "   "
    If intA > 0 Then
        lblConfig = lblConfig & "A: " & intA & "   "
    End If
    If intB > 0 Then
        lblConfig = lblConfig & "B: " & intB & "   "
    End If
    If intC > 0 Then
        lblConfig = lblConfig & "C: " & intC & "   "
    End If
    If intD > 0 Then
        lblConfig = lblConfig & "D: " & intD & "   "
    End If
    If intE > 0 Then
        lblConfig = lblConfig & "E: " & intE & "   "
    End If
    If intF > 0 Then
        lblConfig = lblConfig & "F: " & intF & "   "
    End If
    If intG > 0 Then
        lblConfig = lblConfig & "G: " & intG & "   "
    End If
    If intH > 0 Then
        lblConfig = lblConfig & "H: " & intH & "   "
    End If
    If intI > 0 Then
        lblConfig = lblConfig & "I: " & intI & "   "
    End If
    If intJ > 0 Then
        lblConfig = lblConfig & "J: " & intJ & "   "
    End If
    
    Set rs = CreateObject("ADODB.Recordset")
    strCmd = "select * from Totals"
    rs.Open strCmd, cn
    If Not rs.EOF Then
        txtTotalRegistered = CInt(rs("TotalRegistered"))
        txtTotalOverdue = CInt(rs("TotalOverDue"))
    End If
    rs.Close
End Sub

Private Sub txtInput_Change()
    booValidInput = True
    lblDateTime = Now
    'If txtInput Like "[A-Z]*" And (Len(Trim(strBarCode)) > 0) Then
    If Len(Trim(strBarCode)) = intBarcodeLength Then
        Set rs = CreateObject("ADODB.Recordset")
        strCmd = "select * from Sushi where barcode = '" & strBarCode & "' "
        'Debug.Print "strCmd(" & strCmd & ")"
        rs.Open strCmd, cn
        If Not rs.EOF Then
            intFound = 1
            strStartTime = rs("StartTime")
            intOverDue = rs("OverDue")
        Else
            intFound = 0
        End If
        rs.Close
        
        'strCurrT = FormatDateTime(Now, vbLongTime)
        strCurrT = Now
        'Debug.Print "strCurrT(" & strCurrT & ")"
        
        strPlateKind = Mid(strBarCode, 1, 1)
        'Debug.Print "strPlateKind(" & strPlateKind & ")"
        If strPlateKind = "A" Then
            intMaxTrip = intA
            strWhichColor = strAColor
        ElseIf strPlateKind = "B" Then
            intMaxTrip = intB
            strWhichColor = strBColor
        ElseIf strPlateKind = "C" Then
            intMaxTrip = intC
            strWhichColor = strCColor
        ElseIf strPlateKind = "D" Then
            intMaxTrip = intD
            strWhichColor = strDColor
        ElseIf strPlateKind = "E" Then
            intMaxTrip = intE
            strWhichColor = strEColor
        ElseIf strPlateKind = "F" Then
            intMaxTrip = intF
            strWhichColor = strFColor
        ElseIf strPlateKind = "G" Then
            intMaxTrip = intG
            strWhichColor = strGColor
        ElseIf strPlateKind = "H" Then
            intMaxTrip = intH
            strWhichColor = strHColor
        ElseIf strPlateKind = "I" Then
            intMaxTrip = intI
            strWhichColor = strIColor
        ElseIf strPlateKind = "J" Then
            intMaxTrip = intJ
            strWhichColor = strJColor
        End If
        lblColor(intLblColorIndex).BackColor = strWhichColor
        If intLblColorIndex = INTLBLCOLORINDEXMAX Then
            For i = 0 To INTLBLCOLORINDEXMAX - 1
                lblColor(i).BackColor = lblColor(i + 1).BackColor
            Next
            'txtStatus = txtStatus & vbCrLf & strBarCode
            lblColor(intLblColorIndex).BackColor = &HC0C0C0
        Else
            intLblColorIndex = intLblColorIndex + 1
        End If
        
        If intFound = 0 Then
            Set rsInsert = CreateObject("ADODB.Recordset")
            strCmdInsert = "insert into sushi (BarCode, StartTime, ScanTime, OverDue) " & _
                            "values ('" & strBarCode & "', '" & strCurrT & "', '" & strCurrT & "', 0) "
            'Debug.Print "strCmdInsert(" & strCmdInsert & ")"
            rsInsert.Open strCmdInsert, cn
            txtTotalRegistered = txtTotalRegistered + 1
            txtStatus = txtStatus & " registered"
        Else
            'intMinsTook = TimeDiff("m", strStartTime, strCurrT)
            intMinsTook = DateDiff("n", strStartTime, strCurrT)
            If intMinsTook < 0 Then
                intMinsTook = intMinsTook * -1
            End If
            'Debug.Print "strStartTime(" & strStartTime & ") strCurrT(" & strCurrT & ") intMinsTook(" & intMinsTook & ") intMaxTrip(" & intMaxTrip & ")"
            If intMinsTook > intMaxTrip Then
                'If (intMinsTook - intMaxTrip) <= strTripTime Then
                If (intMinsTook - intMaxTrip) <= (strTripTime * 2) Then     ' Assume eaten plate won't return at least for 2 kaitens
                    txtStatus = txtStatus & " : " & intMinsTook & " min"
                    lblScore.BackColor = vbRed
                    lblScore.Caption = vbCrLf & strBarCode & vbCrLf & "OVERDUE"
                    Play "wav\notify.wav"
                    If intOverDue = 0 Then
                        txtTotalOverdue = txtTotalOverdue + 1
                    End If
                    intOverDue = intOverDue + 1
                    
                    Set rsUpdate = CreateObject("ADODB.Recordset")
                    strCmdUpdate = "update sushi set OverDue = " & intOverDue & " " & _
                                    "where BarCode = '" & strBarCode & "' "
                    'Debug.Print "strCmdUpdate(" & strCmdUpdate & ")"
                    rsUpdate.Open strCmdUpdate, cn
                    
                    txtStatus = txtStatus & " >>> Overdue! (round " & intOverDue & ")"
                    'Debug.Print "Overdue"
                Else
                    Set rsUpdate = CreateObject("ADODB.Recordset")
                    strCmdUpdate = "update sushi set StartTime = '" & strCurrT & "', ScanTime = '" & strCurrT & "', OverDue = 0 " & _
                                    "where BarCode = '" & strBarCode & "' "
                    'Debug.Print "strCmdUpdate(" & strCmdUpdate & ")"
                    rsUpdate.Open strCmdUpdate, cn
                    txtTotalRegistered = txtTotalRegistered + 1
                    txtStatus = txtStatus & " re-registered"
                    lblScore.BackColor = vbGreen
                    lblScore.Caption = vbCrLf & vbCrLf & "OK"
                End If
            Else
                txtStatus = txtStatus & " : " & intMinsTook & " min"
                lblScore.BackColor = vbGreen
                lblScore.Caption = vbCrLf & vbCrLf & "OK"
            End If
        End If
    
        txtStatus = txtStatus & vbCrLf
        If txtInput Like "[A-Z]*" Then  ' Make sure barcode starts w/Alpha
            strBarCode = txtInput
        Else
            strBarCode = ""
            booValidInput = False
        End If
    Else
        If Len(Trim(strBarCode)) = 0 Then  ' Make sure barcode starts w/Alpha
            If txtInput Like "[A-Z]*" Then
                strBarCode = strBarCode & txtInput
            Else
                booValidInput = False
            End If
        Else
            strBarCode = strBarCode & txtInput
        End If
    End If

    If booValidInput = True Then  ' Make sure barcode starts w/Alpha
        txtStatus = txtStatus & txtInput
    End If
    
    txtInput = ""
    
    If Len(txtStatus) > 1000 Then
        intCRPosition = InStr(500, txtStatus, vbCrLf)
        txtStatus = Mid(txtStatus, intCRPosition + 2)
    End If
    Me.Refresh
    txtStatus.SelStart = Len(txtStatus)
End Sub

Sub ConnectDB()
    Set cn = CreateObject("ADODB.Connection")
    cn.ConnectionTimeout = 25
    cn.Provider = "Microsoft.Jet.OLEDB.4.0;Data Source=db1.mdb"
    cn.Open
End Sub

Function TimeDiff(ByVal Interval As String, ByVal Time1 As String, ByVal Time2 As String)
    '(C) Roderick Thompson, April 2001, KLIK4.COM Limited
    Time1 = FormatDateTime(Time1, vbLongTime)
    Time2 = FormatDateTime(Time2, vbLongTime)
    HourTime1 = Hour(Time1)
    HourTime2 = Hour(Time2)
    MinuteTime1 = Minute(Time1)
    MinuteTime2 = Minute(Time2)
    SecondTime1 = Second(Time1)
    SecondTime2 = Second(Time2)
    Time1InSeconds = SecondTime1 + (MinuteTime1 * 60) + (HourTime1 * 3600)
    Time2InSeconds = SecondTime2 + (MinuteTime2 * 60) + (HourTime2 * 3600)
    TimeDifference = Time2InSeconds - Time1InSeconds
    Select Case Interval
        Case "h"
            TimeDifference = TimeDifference / 3600
        Case "m"
            TimeDifference = TimeDifference / 60
    End Select
    TimeDiff = TimeDifference
End Function

Private Sub txtStatus_GotFocus()
    txtInput.SetFocus
End Sub

Private Sub txtTotalOverdue_GotFocus()
    txtInput.SetFocus
End Sub

Private Sub txtTotalRegistered_GotFocus()
    txtInput.SetFocus
End Sub

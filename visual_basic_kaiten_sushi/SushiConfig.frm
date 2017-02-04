VERSION 5.00
Begin VB.Form SushiConfig 
   Caption         =   "Configuration"
   ClientHeight    =   6795
   ClientLeft      =   60
   ClientTop       =   345
   ClientWidth     =   9480
   LinkTopic       =   "Form1"
   ScaleHeight     =   6795
   ScaleWidth      =   9480
   StartUpPosition =   2  'CenterScreen
   Begin VB.CommandButton cmdDel 
      Caption         =   "Del"
      Height          =   375
      Left            =   4560
      MaskColor       =   &H8000000F&
      Style           =   1  'Graphical
      TabIndex        =   65
      TabStop         =   0   'False
      Top             =   1920
      Width           =   615
   End
   Begin VB.CommandButton cmd0 
      Caption         =   "0"
      Height          =   375
      Left            =   5880
      MaskColor       =   &H8000000F&
      Style           =   1  'Graphical
      TabIndex        =   64
      TabStop         =   0   'False
      Top             =   1320
      Width           =   375
   End
   Begin VB.CommandButton cmd9 
      Caption         =   "9"
      Height          =   375
      Left            =   5280
      MaskColor       =   &H8000000F&
      Style           =   1  'Graphical
      TabIndex        =   63
      TabStop         =   0   'False
      Top             =   1320
      Width           =   375
   End
   Begin VB.CommandButton cmd8 
      Caption         =   "8"
      Height          =   375
      Left            =   4680
      MaskColor       =   &H8000000F&
      Style           =   1  'Graphical
      TabIndex        =   62
      TabStop         =   0   'False
      Top             =   1320
      Width           =   375
   End
   Begin VB.CommandButton cmd7 
      Caption         =   "7"
      Height          =   375
      Left            =   4080
      MaskColor       =   &H8000000F&
      Style           =   1  'Graphical
      TabIndex        =   61
      TabStop         =   0   'False
      Top             =   1320
      Width           =   375
   End
   Begin VB.CommandButton cmd6 
      Caption         =   "6"
      Height          =   375
      Left            =   3480
      MaskColor       =   &H8000000F&
      Style           =   1  'Graphical
      TabIndex        =   60
      TabStop         =   0   'False
      Top             =   1320
      Width           =   375
   End
   Begin VB.CommandButton cmd5 
      Caption         =   "5"
      Height          =   375
      Left            =   5880
      MaskColor       =   &H8000000F&
      Style           =   1  'Graphical
      TabIndex        =   59
      TabStop         =   0   'False
      Top             =   720
      Width           =   375
   End
   Begin VB.CommandButton cmd4 
      Caption         =   "4"
      Height          =   375
      Left            =   5280
      MaskColor       =   &H8000000F&
      Style           =   1  'Graphical
      TabIndex        =   58
      TabStop         =   0   'False
      Top             =   720
      Width           =   375
   End
   Begin VB.CommandButton cmd3 
      Caption         =   "3"
      Height          =   375
      Left            =   4680
      MaskColor       =   &H8000000F&
      Style           =   1  'Graphical
      TabIndex        =   57
      TabStop         =   0   'False
      Top             =   720
      Width           =   375
   End
   Begin VB.CommandButton cmd2 
      Caption         =   "2"
      Height          =   375
      Left            =   4080
      MaskColor       =   &H8000000F&
      Style           =   1  'Graphical
      TabIndex        =   56
      TabStop         =   0   'False
      Top             =   720
      Width           =   375
   End
   Begin VB.CommandButton cmd1 
      Caption         =   "1"
      Height          =   375
      Left            =   3480
      MaskColor       =   &H8000000F&
      Style           =   1  'Graphical
      TabIndex        =   55
      TabStop         =   0   'False
      Top             =   720
      Width           =   375
   End
   Begin VB.CommandButton cmdLime 
      BackColor       =   &H0000FF00&
      Height          =   375
      Left            =   3480
      MaskColor       =   &H8000000F&
      Style           =   1  'Graphical
      TabIndex        =   54
      TabStop         =   0   'False
      Top             =   3720
      Width           =   375
   End
   Begin VB.CommandButton cmdYellow 
      BackColor       =   &H0000FFFF&
      Height          =   375
      Left            =   5880
      MaskColor       =   &H8000000F&
      Style           =   1  'Graphical
      TabIndex        =   53
      TabStop         =   0   'False
      Top             =   2520
      Width           =   375
   End
   Begin VB.CommandButton cmdWhite 
      BackColor       =   &H00FFFFFF&
      Height          =   375
      Left            =   3480
      MaskColor       =   &H8000000F&
      Style           =   1  'Graphical
      TabIndex        =   52
      TabStop         =   0   'False
      Top             =   2520
      Width           =   375
   End
   Begin VB.CommandButton cmdTeal 
      BackColor       =   &H00808000&
      Height          =   375
      Left            =   5880
      MaskColor       =   &H8000000F&
      Style           =   1  'Graphical
      TabIndex        =   51
      TabStop         =   0   'False
      Top             =   3120
      Width           =   375
   End
   Begin VB.CommandButton cmdSilver 
      BackColor       =   &H00C0C0C0&
      Height          =   375
      Left            =   4080
      MaskColor       =   &H8000000F&
      Style           =   1  'Graphical
      TabIndex        =   50
      TabStop         =   0   'False
      Top             =   2520
      Width           =   375
   End
   Begin VB.CommandButton cmdRed 
      BackColor       =   &H000000FF&
      Height          =   375
      Left            =   3480
      MaskColor       =   &H8000000F&
      Style           =   1  'Graphical
      TabIndex        =   49
      TabStop         =   0   'False
      Top             =   3120
      Width           =   375
   End
   Begin VB.CommandButton cmdPurple 
      BackColor       =   &H00800080&
      Height          =   375
      Left            =   4680
      MaskColor       =   &H8000000F&
      Style           =   1  'Graphical
      TabIndex        =   48
      TabStop         =   0   'False
      Top             =   3120
      Width           =   375
   End
   Begin VB.CommandButton cmdOlive 
      BackColor       =   &H00008080&
      Height          =   375
      Left            =   5280
      MaskColor       =   &H8000000F&
      Style           =   1  'Graphical
      TabIndex        =   47
      TabStop         =   0   'False
      Top             =   3120
      Width           =   375
   End
   Begin VB.CommandButton cmdNavy 
      BackColor       =   &H00800000&
      Height          =   375
      Left            =   5880
      MaskColor       =   &H8000000F&
      Style           =   1  'Graphical
      TabIndex        =   46
      TabStop         =   0   'False
      Top             =   3720
      Width           =   375
   End
   Begin VB.CommandButton cmdMaroon 
      BackColor       =   &H00000080&
      Height          =   375
      Left            =   3480
      MaskColor       =   &H8000000F&
      Style           =   1  'Graphical
      TabIndex        =   45
      TabStop         =   0   'False
      Top             =   4320
      Width           =   375
   End
   Begin VB.CommandButton cmdGreen 
      BackColor       =   &H0000C000&
      Height          =   375
      Left            =   4080
      MaskColor       =   &H8000000F&
      Style           =   1  'Graphical
      TabIndex        =   44
      TabStop         =   0   'False
      Top             =   3720
      Width           =   375
   End
   Begin VB.CommandButton cmdGray 
      BackColor       =   &H00808080&
      Height          =   375
      Left            =   4680
      MaskColor       =   &H8000000F&
      Style           =   1  'Graphical
      TabIndex        =   43
      TabStop         =   0   'False
      Top             =   2520
      Width           =   375
   End
   Begin VB.CommandButton cmdFuchsia 
      BackColor       =   &H00FF00FF&
      Height          =   375
      Left            =   4080
      MaskColor       =   &H8000000F&
      Style           =   1  'Graphical
      TabIndex        =   42
      TabStop         =   0   'False
      Top             =   3120
      Width           =   375
   End
   Begin VB.CommandButton cmdBlue 
      BackColor       =   &H00FF0000&
      Height          =   375
      Left            =   5280
      MaskColor       =   &H8000000F&
      Style           =   1  'Graphical
      TabIndex        =   41
      TabStop         =   0   'False
      Top             =   3720
      Width           =   375
   End
   Begin VB.CommandButton cmdBlack 
      BackColor       =   &H00000000&
      Height          =   375
      Left            =   5280
      MaskColor       =   &H8000000F&
      Style           =   1  'Graphical
      TabIndex        =   40
      TabStop         =   0   'False
      Top             =   2520
      Width           =   375
   End
   Begin VB.CommandButton cmdAqua 
      BackColor       =   &H00FFFF00&
      Height          =   375
      Left            =   4680
      MaskColor       =   &H8000000F&
      Style           =   1  'Graphical
      TabIndex        =   39
      TabStop         =   0   'False
      Top             =   3720
      Width           =   375
   End
   Begin VB.TextBox txtJColor 
      Height          =   285
      Left            =   2040
      TabIndex        =   21
      Top             =   4560
      Width           =   855
   End
   Begin VB.TextBox txtJ 
      Alignment       =   1  'Right Justify
      Height          =   285
      Left            =   960
      TabIndex        =   20
      Text            =   "0"
      Top             =   4560
      Width           =   615
   End
   Begin VB.TextBox txtIColor 
      Height          =   285
      Left            =   2040
      TabIndex        =   19
      Top             =   4200
      Width           =   855
   End
   Begin VB.TextBox txtI 
      Alignment       =   1  'Right Justify
      Height          =   285
      Left            =   960
      TabIndex        =   18
      Text            =   "0"
      Top             =   4200
      Width           =   615
   End
   Begin VB.TextBox txtHColor 
      Height          =   285
      Left            =   2040
      TabIndex        =   17
      Top             =   3840
      Width           =   855
   End
   Begin VB.TextBox txtH 
      Alignment       =   1  'Right Justify
      Height          =   285
      Left            =   960
      TabIndex        =   16
      Text            =   "0"
      Top             =   3840
      Width           =   615
   End
   Begin VB.TextBox txtGColor 
      Height          =   285
      Left            =   2040
      TabIndex        =   15
      Top             =   3480
      Width           =   855
   End
   Begin VB.TextBox txtG 
      Alignment       =   1  'Right Justify
      Height          =   285
      Left            =   960
      TabIndex        =   14
      Text            =   "0"
      Top             =   3480
      Width           =   615
   End
   Begin VB.TextBox txtFColor 
      Height          =   285
      Left            =   2040
      TabIndex        =   13
      Top             =   3120
      Width           =   855
   End
   Begin VB.TextBox txtF 
      Alignment       =   1  'Right Justify
      Height          =   285
      Left            =   960
      TabIndex        =   12
      Text            =   "0"
      Top             =   3120
      Width           =   615
   End
   Begin VB.TextBox txtEColor 
      Height          =   285
      Left            =   2040
      TabIndex        =   11
      Top             =   2760
      Width           =   855
   End
   Begin VB.TextBox txtE 
      Alignment       =   1  'Right Justify
      Height          =   285
      Left            =   960
      TabIndex        =   10
      Text            =   "0"
      Top             =   2760
      Width           =   615
   End
   Begin VB.TextBox txtDColor 
      Height          =   285
      Left            =   2040
      TabIndex        =   9
      Top             =   2400
      Width           =   855
   End
   Begin VB.TextBox txtD 
      Alignment       =   1  'Right Justify
      Height          =   285
      Left            =   960
      TabIndex        =   8
      Text            =   "0"
      Top             =   2400
      Width           =   615
   End
   Begin VB.TextBox txtCColor 
      Height          =   285
      Left            =   2040
      TabIndex        =   7
      Top             =   2040
      Width           =   855
   End
   Begin VB.TextBox txtC 
      Alignment       =   1  'Right Justify
      Height          =   285
      Left            =   960
      TabIndex        =   6
      Text            =   "0"
      Top             =   2040
      Width           =   615
   End
   Begin VB.TextBox txtBColor 
      Height          =   285
      Left            =   2040
      TabIndex        =   5
      Top             =   1680
      Width           =   855
   End
   Begin VB.TextBox txtB 
      Alignment       =   1  'Right Justify
      Height          =   285
      Left            =   960
      TabIndex        =   4
      Text            =   "0"
      Top             =   1680
      Width           =   615
   End
   Begin VB.TextBox txtAColor 
      Height          =   285
      Left            =   2040
      TabIndex        =   3
      Top             =   1320
      Width           =   855
   End
   Begin VB.TextBox txtA 
      Alignment       =   1  'Right Justify
      Height          =   285
      Left            =   960
      TabIndex        =   2
      Text            =   "0"
      Top             =   1320
      Width           =   615
   End
   Begin VB.PictureBox Picture1 
      Align           =   2  'Align Bottom
      BackColor       =   &H00FFFFFF&
      Height          =   1755
      Left            =   0
      Picture         =   "SushiConfig.frx":0000
      ScaleHeight     =   1695
      ScaleWidth      =   9420
      TabIndex        =   33
      Top             =   5040
      Width           =   9480
   End
   Begin VB.CommandButton cmdSave 
      Caption         =   "&Save"
      Height          =   375
      Left            =   6960
      TabIndex        =   31
      Top             =   4440
      Width           =   855
   End
   Begin VB.TextBox txtTripTime 
      Alignment       =   1  'Right Justify
      Height          =   285
      Left            =   5040
      TabIndex        =   1
      Top             =   120
      Width           =   615
   End
   Begin VB.TextBox txtBarcodeLength 
      Alignment       =   1  'Right Justify
      Height          =   285
      Left            =   1560
      TabIndex        =   0
      Top             =   120
      Width           =   615
   End
   Begin VB.CommandButton cmdClose 
      Caption         =   "&Close"
      Height          =   375
      Left            =   8280
      TabIndex        =   32
      Top             =   4440
      Width           =   855
   End
   Begin VB.Label Label13 
      Caption         =   "min"
      Height          =   255
      Left            =   5760
      TabIndex        =   70
      Top             =   120
      Width           =   255
   End
   Begin VB.Line Line11 
      BorderColor     =   &H00808080&
      X1              =   120
      X2              =   3000
      Y1              =   4920
      Y2              =   4920
   End
   Begin VB.Label Label12 
      Caption         =   "Click a plate and color to set color."
      Height          =   375
      Left            =   6960
      TabIndex        =   69
      Top             =   3120
      Width           =   2055
   End
   Begin VB.Label Label11 
      Caption         =   "Click a plate and Del to clear."
      Height          =   375
      Left            =   6960
      TabIndex        =   68
      Top             =   1800
      Width           =   2055
   End
   Begin VB.Label Label1 
      Caption         =   "Click a plate and number(s) to set max time."
      Height          =   375
      Left            =   6960
      TabIndex        =   67
      Top             =   840
      Width           =   2055
   End
   Begin VB.Line Line10 
      BorderColor     =   &H00808080&
      X1              =   120
      X2              =   3000
      Y1              =   1200
      Y2              =   1200
   End
   Begin VB.Line Line9 
      BorderColor     =   &H00808080&
      X1              =   120
      X2              =   3000
      Y1              =   5160
      Y2              =   5160
   End
   Begin VB.Line Line8 
      BorderColor     =   &H00808080&
      X1              =   120
      X2              =   3000
      Y1              =   600
      Y2              =   600
   End
   Begin VB.Line Line7 
      BorderColor     =   &H00808080&
      X1              =   120
      X2              =   120
      Y1              =   600
      Y2              =   4920
   End
   Begin VB.Line Line6 
      BorderColor     =   &H00808080&
      X1              =   3000
      X2              =   3000
      Y1              =   600
      Y2              =   4920
   End
   Begin VB.Line Line5 
      BorderColor     =   &H00808080&
      X1              =   3360
      X2              =   6360
      Y1              =   4800
      Y2              =   4800
   End
   Begin VB.Line Line4 
      BorderColor     =   &H00808080&
      X1              =   3360
      X2              =   6360
      Y1              =   2040
      Y2              =   2040
   End
   Begin VB.Line Line3 
      BorderColor     =   &H00808080&
      X1              =   3360
      X2              =   6360
      Y1              =   600
      Y2              =   600
   End
   Begin VB.Line Line2 
      BorderColor     =   &H00808080&
      X1              =   6360
      X2              =   6360
      Y1              =   600
      Y2              =   4800
   End
   Begin VB.Line Line1 
      BorderColor     =   &H00808080&
      X1              =   3360
      X2              =   3360
      Y1              =   600
      Y2              =   4800
   End
   Begin VB.Label Label4 
      Caption         =   "Plates"
      ForeColor       =   &H00C00000&
      Height          =   255
      Index           =   2
      Left            =   240
      TabIndex        =   66
      Top             =   720
      Width           =   495
   End
   Begin VB.Label Label4 
      Caption         =   "Color"
      Height          =   255
      Index           =   1
      Left            =   2280
      TabIndex        =   38
      Top             =   720
      Width           =   375
   End
   Begin VB.Label Label10 
      Alignment       =   2  'Center
      Caption         =   "J:"
      ForeColor       =   &H00C00000&
      Height          =   255
      Index           =   4
      Left            =   360
      TabIndex        =   37
      Top             =   4560
      Width           =   255
   End
   Begin VB.Label Label10 
      Alignment       =   2  'Center
      Caption         =   "I:"
      ForeColor       =   &H00C00000&
      Height          =   255
      Index           =   3
      Left            =   360
      TabIndex        =   36
      Top             =   4200
      Width           =   255
   End
   Begin VB.Label Label10 
      Alignment       =   2  'Center
      Caption         =   "H:"
      ForeColor       =   &H00C00000&
      Height          =   255
      Index           =   2
      Left            =   360
      TabIndex        =   35
      Top             =   3840
      Width           =   255
   End
   Begin VB.Label Label10 
      Alignment       =   2  'Center
      Caption         =   "G:"
      ForeColor       =   &H00C00000&
      Height          =   255
      Index           =   1
      Left            =   360
      TabIndex        =   34
      Top             =   3480
      Width           =   255
   End
   Begin VB.Label Label10 
      Alignment       =   2  'Center
      Caption         =   "F:"
      ForeColor       =   &H00C00000&
      Height          =   255
      Index           =   0
      Left            =   360
      TabIndex        =   30
      Top             =   3120
      Width           =   255
   End
   Begin VB.Label Label9 
      Alignment       =   2  'Center
      Caption         =   "E:"
      ForeColor       =   &H00C00000&
      Height          =   255
      Left            =   360
      TabIndex        =   29
      Top             =   2760
      Width           =   255
   End
   Begin VB.Label Label8 
      Alignment       =   2  'Center
      Caption         =   "D:"
      ForeColor       =   &H00C00000&
      Height          =   255
      Left            =   360
      TabIndex        =   28
      Top             =   2400
      Width           =   255
   End
   Begin VB.Label Label7 
      Alignment       =   2  'Center
      Caption         =   "C:"
      ForeColor       =   &H00C00000&
      Height          =   255
      Left            =   360
      TabIndex        =   27
      Top             =   2040
      Width           =   255
   End
   Begin VB.Label Label6 
      Alignment       =   2  'Center
      Caption         =   "B:"
      ForeColor       =   &H00C00000&
      Height          =   255
      Left            =   360
      TabIndex        =   26
      Top             =   1680
      Width           =   255
   End
   Begin VB.Label Label5 
      Alignment       =   2  'Center
      Caption         =   "A:"
      ForeColor       =   &H00C00000&
      Height          =   255
      Left            =   360
      TabIndex        =   25
      Top             =   1320
      Width           =   255
   End
   Begin VB.Label Label4 
      Caption         =   "Max Time (in Minutes)"
      Height          =   495
      Index           =   0
      Left            =   960
      TabIndex        =   24
      Top             =   720
      Width           =   855
   End
   Begin VB.Label Label3 
      Alignment       =   1  'Right Justify
      Caption         =   "Kaiten Total Minutes:"
      Height          =   255
      Left            =   3360
      TabIndex        =   23
      Top             =   120
      Width           =   1575
   End
   Begin VB.Label Label2 
      Alignment       =   1  'Right Justify
      Caption         =   "Barcode Length:"
      Height          =   255
      Left            =   240
      TabIndex        =   22
      Top             =   120
      Width           =   1215
   End
End
Attribute VB_Name = "SushiConfig"
Attribute VB_GlobalNameSpace = False
Attribute VB_Creatable = False
Attribute VB_PredeclaredId = True
Attribute VB_Exposed = False
Dim cn
Dim strFocus

Private Sub AssignNumber(numb)
    If strFocus = "A" Then
        If numb = "" Then
            txtA = numb
        End If
        txtA = txtA & numb
    ElseIf strFocus = "B" Then
        If numb = "" Then
            txtB = numb
        End If
        txtB = txtB & numb
    ElseIf strFocus = "C" Then
        If numb = "" Then
            txtC = numb
        End If
        txtC = txtC & numb
    ElseIf strFocus = "D" Then
        If numb = "" Then
            txtD = numb
        End If
        txtD = txtD & numb
    ElseIf strFocus = "E" Then
        If numb = "" Then
            txtE = numb
        End If
        txtE = txtE & numb
    ElseIf strFocus = "F" Then
        If numb = "" Then
            txtF = numb
        End If
        txtF = txtF & numb
    ElseIf strFocus = "G" Then
        If numb = "" Then
            txtG = numb
        End If
        txtG = txtG & numb
    ElseIf strFocus = "H" Then
        If numb = "" Then
            txtH = numb
        End If
        txtH = txtH & numb
    ElseIf strFocus = "I" Then
        If numb = "" Then
            txtI = numb
        End If
        txtI = txtI & numb
    ElseIf strFocus = "J" Then
        If numb = "" Then
            txtJ = numb
        End If
        txtJ = txtJ & numb
    ElseIf strFocus = "BarcodeLength" Then
        If numb = "" Then
            txtBarcodeLength = numb
        End If
        txtBarcodeLength = txtBarcodeLength & numb
    ElseIf strFocus = "TripTime" Then
        If numb = "" Then
            txtTripTime = numb
        End If
        txtTripTime = txtTripTime & numb
    End If
End Sub

Private Sub AssignColor(colr)
    strFocus = Mid(strFocus, 2, 1)
    If strFocus = "A" Then
        txtAColor = colr
        txtAColor.BackColor = colr
    ElseIf strFocus = "B" Then
        txtBColor = colr
        txtBColor.BackColor = colr
    ElseIf strFocus = "C" Then
        txtCColor = colr
        txtCColor.BackColor = colr
    ElseIf strFocus = "D" Then
        txtDColor = colr
        txtDColor.BackColor = colr
    ElseIf strFocus = "E" Then
        txtEColor = colr
        txtEColor.BackColor = colr
    ElseIf strFocus = "F" Then
        txtFColor = colr
        txtFColor.BackColor = colr
    ElseIf strFocus = "G" Then
        txtGColor = colr
        txtGColor.BackColor = colr
    ElseIf strFocus = "H" Then
        txtHColor = colr
        txtHColor.BackColor = colr
    ElseIf strFocus = "I" Then
        txtIColor = colr
        txtIColor.BackColor = colr
    ElseIf strFocus = "J" Then
        txtJColor = colr
        txtJColor.BackColor = colr
    End If
End Sub

Private Sub cmd1_Click()
    AssignNumber ("1")
End Sub

Private Sub cmd2_Click()
    AssignNumber ("2")
End Sub

Private Sub cmd3_Click()
    AssignNumber ("3")
End Sub

Private Sub cmd4_Click()
    AssignNumber ("4")
End Sub

Private Sub cmd5_Click()
    AssignNumber ("5")
End Sub

Private Sub cmd6_Click()
    AssignNumber ("6")
End Sub

Private Sub cmd7_Click()
    AssignNumber ("7")
End Sub

Private Sub cmd8_Click()
    AssignNumber ("8")
End Sub

Private Sub cmd9_Click()
    AssignNumber ("9")
End Sub

Private Sub cmd0_Click()
    AssignNumber ("0")
End Sub

Private Sub cmdAqua_Click()
    AssignColor ("&HFFFF00")
End Sub

Private Sub cmdBlack_Click()
    AssignColor ("&H000000")
End Sub

Private Sub cmdBlue_Click()
    AssignColor ("&HFF0000")
End Sub

Private Sub cmdClose_Click()
    cn.Close
    End
End Sub

Private Sub cmdDel_Click()
    If Len(strFocus) = 1 Or Len(strFocus) > 2 Then
        AssignNumber ("")
    Else
        AssignColor ("&HC0C0C0")
    End If
End Sub

Private Sub cmdFuchsia_Click()
    AssignColor ("&HFF00FF")
End Sub

Private Sub cmdGray_Click()
    AssignColor ("&H808080")
End Sub

Private Sub cmdGreen_Click()
    AssignColor ("&H00FF00")
End Sub

Private Sub cmdLime_Click()
    AssignColor ("&H00FF80")
End Sub

Private Sub cmdMaroon_Click()
    AssignColor ("&H000080")
End Sub

Private Sub cmdNavy_Click()
    AssignColor ("&HA00000")
End Sub

Private Sub cmdOlive_Click()
    AssignColor ("&H008080")
End Sub

Private Sub cmdPurple_Click()
    AssignColor ("&H800040")
End Sub

Private Sub cmdRed_Click()
    'AssignColor ("Red")
    AssignColor ("&H0000FF")
End Sub

Private Sub cmdSave_Click()
    answer = MsgBox("Are you sure?  It will overwrite the current values.", vbYesNo, "Save")
    If answer = vbYes Then
        Set rsUpdate = CreateObject("ADODB.Recordset")
        strCmdUpdate = "update TimeConfig set " & _
                        "BarcodeLength = " & txtBarcodeLength & ", " & _
                        "TripTime = " & txtTripTime & ", " & _
                        "A = " & txtA & ", " & _
                        "B = " & txtB & ", " & _
                        "C = " & txtC & ", " & _
                        "D = " & txtD & ", " & _
                        "E = " & txtE & ", " & _
                        "F = " & txtF & ", " & _
                        "G = " & txtG & ", " & _
                        "H = " & txtH & ", " & _
                        "I = " & txtI & ", " & _
                        "J = " & txtJ & ", " & _
                        "AColor = '" & txtAColor & "', " & _
                        "BColor = '" & txtBColor & "', " & _
                        "CColor = '" & txtCColor & "', " & _
                        "DColor = '" & txtDColor & "', " & _
                        "EColor = '" & txtEColor & "', " & _
                        "FColor = '" & txtFColor & "', " & _
                        "GColor = '" & txtGColor & "', " & _
                        "HColor = '" & txtHColor & "', " & _
                        "IColor = '" & txtIColor & "', " & _
                        "JColor = '" & txtJColor & "' "
        'Debug.Print "strCmdUpdate(" & strCmdUpdate & ")"
        rsUpdate.Open strCmdUpdate, cn
    End If
    Call SelectConfigTable
End Sub

Private Sub cmdSilver_Click()
    AssignColor ("&HC0C0C0")
End Sub

Private Sub cmdTeal_Click()
    AssignColor ("&H808000")
End Sub

Private Sub cmdWhite_Click()
    AssignColor ("&HFFFFFF")
End Sub

Private Sub cmdYellow_Click()
    AssignColor ("&H00FFFF")
End Sub

Private Sub Form_Load()
    Call ConnectDB
    Call SelectConfigTable
End Sub

Sub SelectConfigTable()
    Set rs = CreateObject("ADODB.Recordset")
    strCmd = "select * from TimeConfig"
    rs.Open strCmd, cn
    If Not rs.EOF Then
        txtBarcodeLength = CInt(Trim(rs("BarcodeLength")))
        txtTripTime = CInt(Trim(rs("TripTime")))
        txtA = CInt(Trim(rs("A")))
        txtB = CInt(Trim(rs("B")))
        txtC = CInt(Trim(rs("C")))
        txtD = CInt(Trim(rs("D")))
        txtE = CInt(Trim(rs("E")))
        txtF = CInt(Trim(rs("F")))
        txtG = CInt(Trim(rs("G")))
        txtH = CInt(Trim(rs("H")))
        txtI = CInt(Trim(rs("I")))
        txtJ = CInt(Trim(rs("J")))
        If Not IsNull(rs("AColor")) And rs("AColor") <> "" Then
            txtAColor = rs("AColor")
            txtAColor.BackColor = rs("AColor")
        End If
        If Not IsNull(rs("BColor")) And rs("BColor") <> "" Then
            txtBColor = rs("BColor")
            txtBColor.BackColor = rs("BColor")
        End If
        If Not IsNull(rs("CColor")) And rs("CColor") <> "" Then
            txtCColor = rs("CColor")
            txtCColor.BackColor = rs("CColor")
        End If
        If Not IsNull(rs("DColor")) And rs("DColor") <> "" Then
            txtDColor = rs("DColor")
            txtDColor.BackColor = rs("DColor")
        End If
        If Not IsNull(rs("EColor")) And rs("EColor") <> "" Then
            txtEColor = rs("EColor")
            txtEColor.BackColor = rs("EColor")
        End If
        If Not IsNull(rs("FColor")) And rs("FColor") <> "" Then
            txtFColor = rs("FColor")
            txtFColor.BackColor = rs("FColor")
        End If
        If Not IsNull(rs("GColor")) And rs("GColor") <> "" Then
            txtGColor = rs("GColor")
            txtGColor.BackColor = rs("GColor")
        End If
        If Not IsNull(rs("HColor")) And rs("HColor") <> "" Then
            txtHColor = rs("HColor")
            txtHColor.BackColor = rs("HColor")
        End If
        If Not IsNull(rs("IColor")) And rs("IColor") <> "" Then
            txtIColor = rs("IColor")
            txtIColor.BackColor = rs("IColor")
        End If
        If Not IsNull(rs("JColor")) And rs("JColor") <> "" Then
            txtJColor = rs("JColor")
            txtJColor.BackColor = rs("JColor")
        End If
        'txtStoreName = Trim(rs("StoreName"))
    End If
    rs.Close
End Sub

Sub ConnectDB()
    Set cn = CreateObject("ADODB.Connection")
    cn.ConnectionTimeout = 25
    cn.Provider = "Microsoft.Jet.OLEDB.4.0;Data Source=db1.mdb"
    cn.Open
End Sub

Private Sub txtA_LostFocus()
    strFocus = "A"
End Sub

Private Sub txtB_LostFocus()
    strFocus = "B"
End Sub

Private Sub txtBarcodeLength_LostFocus()
    strFocus = "BarcodeLength"
End Sub

Private Sub txtC_LostFocus()
    strFocus = "C"
End Sub

Private Sub txtD_LostFocus()
    strFocus = "D"
End Sub

Private Sub txtE_LostFocus()
    strFocus = "E"
End Sub

Private Sub txtF_LostFocus()
    strFocus = "F"
End Sub

Private Sub txtG_LostFocus()
    strFocus = "G"
End Sub

Private Sub txtH_LostFocus()
    strFocus = "H"
End Sub

Private Sub txtI_LostFocus()
    strFocus = "I"
End Sub

Private Sub txtJ_LostFocus()
    strFocus = "J"
End Sub

Private Sub txtAColor_LostFocus()
    strFocus = "AA"
End Sub

Private Sub txtBColor_LostFocus()
    strFocus = "BB"
End Sub

Private Sub txtCColor_LostFocus()
    strFocus = "CC"
End Sub

Private Sub txtDColor_LostFocus()
    strFocus = "DD"
End Sub

Private Sub txtEColor_LostFocus()
    strFocus = "EE"
End Sub

Private Sub txtFColor_LostFocus()
    strFocus = "FF"
End Sub

Private Sub txtGColor_LostFocus()
    strFocus = "GG"
End Sub

Private Sub txtHColor_LostFocus()
    strFocus = "HH"
End Sub

Private Sub txtIColor_LostFocus()
    strFocus = "II"
End Sub

Private Sub txtJColor_LostFocus()
    strFocus = "JJ"
End Sub

Private Sub txtTripTime_LostFocus()
    strFocus = "TripTime"
End Sub

Attribute VB_Name = "Module1"
Sub cmdAnalyse_Click()

    Dim strRepFicA As String, strRepFicB As String
    Dim wbFicA As Workbook, wbFicB As Workbook, wbFicAna As Workbook
    Dim wsFicA As Worksheet, wsFicB As Worksheet, wsFicAna As Worksheet
    Dim lgLig As Long, lgCol As Long
    Dim lgLigDeb As Long
    
    ' Répertoire et Fichier
    strRepFicA = ThisWorkbook.Path & "\" & "Fichier A.xls"
    strRepFicB = ThisWorkbook.Path & "\" & "Fichier B.xls"

    ' Classeur d'analyse
    Set wbFicAna = ThisWorkbook
    Set wsFicAna = wbFicAna.ActiveSheet
    
    ' Vérifier que les fichiers A et B se trouvent dans le répertoire
    If Dir(strRepFicA) = "" Or Dir(strRepFicB) = "" Then
        MsgBox "Le fichier A et/ou le fichier B sont introuvables", vbCritical + vbOKOnly, "Problème de fichiers..."
        Exit Sub
    End If
    
    Application.ScreenUpdating = False
    
    ' Ouverture du fichier A et définition de la feuille de traitement
    Set wbFicA = Workbooks.Open(Filename:=ThisWorkbook.Path & "\" & "Fichier A.xls")
    Set wsFicA = wbFicA.Worksheets("Feuil1")
    
    ' Ouverture du fichier B et définition de la feuille de traitement
    Set wbFicB = Workbooks.Open(Filename:=ThisWorkbook.Path & "\" & "Fichier B.xls")
    Set wsFicB = wbFicB.Worksheets("Feuil1")
    
    ' Vider les lignes du fichier d'analyse
    wsFicAna.Range("A2:AO" & Cells.Rows.Count).ClearContents
    
    ' Première ligne d'affichage des résultats dans le fichier d'analyse
    lgLigDeb = 2
    
    ' Traitement des lignes des 2 fichiers
    ' Lignes B: 2 à 1250
    For lgLig = 2 To 1250
        ' Colonnes : D à D
        For lgCol = 4 To 4
            ' équivalence trouvée
            equiv = False
            ' Première ligne A
            lgLig2 = 6
            ' Lignes A: 2 à 1250
            Do
                ' Une différence est trouvée dans une ligne
                If wsFicA.Cells(lgLig2, lgCol).Value = -wsFicB.Cells(lgLig, lgCol + 1).Value Then
                    If wsFicB.Cells(lgLig, lgCol + 1).Value <> "" Then
                        ' Vert
                        wsFicAna.Range("A" & lgLigDeb + 1).Interior.ColorIndex = 4
                    
                        ' Affichage du nom du fichier A en colonne A
                        wsFicAna.Range("A" & lgLigDeb).Value = wbFicA.Name
                        ' Copier la ligne du fichier A dans le fichier d'analyse
                        wsFicA.Range("A" & lgLig2 & ":" & "D" & lgLig2).Copy _
                            Destination:=wsFicAna.Range("D" & lgLigDeb)
                    
                        ' Affichage du nom du fichier B en colonne A
                        wsFicAna.Range("A" & lgLigDeb + 1).Value = wbFicB.Name
                        ' Copier la ligne du fichier B dans le fichier d'analyse
                        wsFicB.Range("A" & lgLig & ":" & "E" & lgLig).Copy _
                            Destination:=wsFicAna.Range("D" & lgLigDeb + 1)
                
                        lgLigDeb = lgLigDeb + 3
                        lgLig2 = 1251
                        equiv = True
                    Else
                        lgLig2 = 1251
                    End If
                Else
                    lgLig2 = lgLig2 + 1
                End If
            Loop Until lgLig2 > 1250
            
            If equiv = False Then
                ' Rouge
                wsFicAna.Range("A" & lgLigDeb).Interior.ColorIndex = 3
                ' Affichage du nom du fichier B en colonne A
                wsFicAna.Range("A" & lgLigDeb).Value = wbFicB.Name
                ' Copier la ligne du fichier B dans le fichier d'analyse
                wsFicB.Range("A" & lgLig & ":" & "E" & lgLig).Copy _
                    Destination:=wsFicAna.Range("D" & lgLigDeb)
                    
                lgLigDeb = lgLigDeb + 2
            End If
                
        Next lgCol
    Next lgLig
    
    ' Fermer les fichiers A et B
    wbFicA.Close savechanges:=False
    wbFicB.Close savechanges:=False
    
    MsgBox "Traitement terminé"
    
    Application.ScreenUpdating = True
End Sub

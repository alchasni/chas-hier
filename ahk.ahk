#Requires AutoHotkey v2.0

projectPath := "D:\project\xampp\htdocs\chas-hier"
xamppStart := "D:\project\xampp\xampp_start.exe"
xamppStop  := "D:\project\xampp\xampp_stop.exe"
phpExe     := "D:\project\xampp\php\php.exe"
global phpPID := 0
global isRunning := false

myGui := Gui("+AlwaysOnTop", "Chas-hier Launcher")
myGui.MarginX := 30
myGui.MarginY := 30
myGui.BackColor := "F3F3F3"

myGui.SetFont("s12", "Segoe UI")
btnToggle := myGui.Add("Button", "w240 h50", "▶ Start Server")
btnToggle.OnEvent("Click", ToggleServer)

myGui.SetFont("s10 bold", "Segoe UI")
lblStatus := myGui.Add("Edit", "xp yp+70 w260 h60 Center ReadOnly -E0x200 -VScroll -HScroll", "⚪ Idle")

DetectServerState()
myGui.Show("w320 h180 Center")

ToggleServer(*) {
    global isRunning
    if !isRunning
        StartServer()
    else
        StopServer()
}

StartServer() {
    global phpExe, projectPath, xamppStart, phpPID, lblStatus, btnToggle, isRunning

    btnToggle.Enabled := false
    lblStatus.Value := "⏳ Starting..."
    lblStatus.Opt("cFFA500")

    if FileExist(xamppStart)
        Run(xamppStart, , "Hide")
    Sleep(5000)

    ip := GetLocalIP()
    Run(
        Format('cmd.exe /c cd /d "{}" && "{}" artisan serve --host=0.0.0.0 --port=8000',
        projectPath, phpExe), , "Hide", &phpPID
    )

    Sleep(2000)
    lblStatus.Value := "✅ Running at: `nhttp://" ip ":8000"
    lblStatus.Opt("c008000")
    isRunning := true
    btnToggle.Text := "⏸ Stop Server"
    btnToggle.Enabled := true
}

StopServer() {
    global phpPID, xamppStop, lblStatus, btnToggle, isRunning

    btnToggle.Enabled := false
    lblStatus.Value := "⏳ Stopping..."
    lblStatus.Opt("cFFA500")

    if (phpPID) {
        try ProcessClose(phpPID)
        phpPID := 0
    }
    RunWait("taskkill /f /im php.exe", , "Hide")

    if FileExist(xamppStop)
        Run(xamppStop, , "Hide")
    Sleep(2000)

    lblStatus.Value := "🛑 Stopped"
    lblStatus.Opt("cFF0000")
    isRunning := false
    btnToggle.Text := "▶ Start Server"
    btnToggle.Enabled := true
}

DetectServerState() {
    global isRunning, phpPID, btnToggle, lblStatus

    output := RunWaitOne('tasklist /FI "IMAGENAME eq php.exe" /FO CSV /NH')
    if InStr(output, "php.exe") {
        isRunning := true
        btnToggle.Text := "⏸ Stop Server"
        ip := GetLocalIP()
        lblStatus.Value := "✅ Running at `nhttp://" ip ":8000"
        lblStatus.Opt("c008000")
    } else {
        isRunning := false
        btnToggle.Text := "▶ Start Server"
        lblStatus.Value := "⚪ Idle"
        lblStatus.Opt("c808080")
    }
}

GetLocalIP() {
    output := RunWaitOne('ipconfig | findstr "IPv4 Address"')
    ; Extract the last part after colon
    for , line in StrSplit(output, "`n") {
        if InStr(line, "IPv4") {
            parts := StrSplit(line, ":")
            return Trim(parts[2])
        }
    }
    return "127.0.0.1"
}

RunWaitOne(command) {
    shell := ComObject("WScript.Shell")
    exec := shell.Exec("cmd /c " command)
    return exec.StdOut.ReadAll()
}

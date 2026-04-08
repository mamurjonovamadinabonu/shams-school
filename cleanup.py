import ftplib

# FTP Credentials
FTP_HOST = 'ftpupload.net'
FTP_USER = 'if0_41601500'
FTP_PASS = 'cnsapKYkdIq'
REMOTE_ROOT = 'htdocs'

def main():
    try:
        print(f"Connecting to {FTP_HOST}...")
        ftp = ftplib.FTP(FTP_HOST)
        ftp.login(user=FTP_USER, passwd=FTP_PASS)
        print("Login successful.")

        ftp.cwd(REMOTE_ROOT)
        files = ftp.nlst()
        print(f"Files in htdocs: {files}")

        # List of default InfinityFree files to remove
        defaults = ['index2.html', 'default.php', 'parking.php']
        
        for d in defaults:
            if d in files:
                print(f"Deleting default file: {d}")
                ftp.delete(d)
        
        ftp.quit()
        print("Cleanup finished!")
    except Exception as e:
        print(f"Error: {e}")

if __name__ == "__main__":
    main()

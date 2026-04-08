import os
import ftplib

# Alwaysdata FTP Credentials
FTP_HOST = 'ftp-shams-edu.alwaysdata.net'
FTP_USER = 'shams-edu'
FTP_PASS = 'Vu47EPHRb!auVG_'
LOCAL_DIR = r'C:\Users\Madinabonu\Desktop\5 loiha'
REMOTE_ROOT = 'www' # Alwaysdata default web root

def upload_directory(ftp, local_path, remote_path):
    print(f"Entering directory: {local_path}")
    
    # Ensure remote directory exists
    try:
        ftp.mkd(remote_path)
        print(f"Created remote directory: {remote_path}")
    except ftplib.error_perm:
        # Directory already exists
        pass

    for item in os.listdir(local_path):
        # Skip hidden files and git/node_modules/temp scripts
        if item.startswith('.') or item == 'node_modules' or item.endswith('.py'):
            continue
            
        l_item_path = os.path.join(local_path, item)
        r_item_path = f"{remote_path}/{item}"

        if os.path.isfile(l_item_path):
            print(f"Uploading file: {l_item_path} -> {r_item_path}")
            with open(l_item_path, 'rb') as f:
                ftp.storbinary(f'STOR {r_item_path}', f)
        elif os.path.isdir(l_item_path):
            upload_directory(ftp, l_item_path, r_item_path)

def main():
    try:
        print(f"Connecting to {FTP_HOST} via FTPS...")
        ftp = ftplib.FTP_TLS(FTP_HOST) # Use Secure FTP
        ftp.login(user=FTP_USER, passwd=FTP_PASS)
        ftp.prot_p() # Secure the data channel
        print("Login successful and data channel secured.")

        # Alwaysdata usually has a www directory
        upload_directory(ftp, LOCAL_DIR, REMOTE_ROOT)
        
        ftp.quit()
        print("Alwaysdata Deployment finished successfully!")
    except Exception as e:
        print(f"Error: {e}")

if __name__ == "__main__":
    main()

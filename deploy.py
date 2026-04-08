import os
import ftplib

# FTP Credentials
FTP_HOST = 'ftpupload.net'
FTP_USER = 'if0_41601500'
FTP_PASS = 'cnsapKYkdIq'
LOCAL_DIR = r'C:\Users\Madinabonu\Desktop\5 loiha'
REMOTE_ROOT = 'htdocs'

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
        # Skip hidden files and git/node_modules
        if item.startswith('.') or item == 'node_modules':
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
        print(f"Connecting to {FTP_HOST}...")
        ftp = ftplib.FTP(FTP_HOST)
        ftp.login(user=FTP_USER, passwd=FTP_PASS)
        print("Login successful.")

        upload_directory(ftp, LOCAL_DIR, REMOTE_ROOT)
        
        ftp.quit()
        print("Deployment finished successfully!")
    except Exception as e:
        print(f"Error: {e}")

if __name__ == "__main__":
    main()

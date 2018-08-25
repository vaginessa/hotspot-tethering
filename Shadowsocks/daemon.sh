echo "$(date +'%Y-%m-%d %H:%M:%S') 守护脚本开始运行. .."
echo $$ > ${0%/*}/daemon.pid
while true
do
if [ ! -f $0 ]; then
  echo "$(date +'%Y-%m-%d %H:%M:%S') 守护脚本文件被删除自动退出..."
  kill $$
  break
  exit
fi
if [[ -f /system/bin/pgrep || -f /system/xbin/pgrep && -f /system/bin/sed || -f /system/xbin/sed ]]; then
  dnsforwarder_status=$(pgrep dnsforwarder)
  if [ $? -eq 0 ]; then
    pid=$(echo "$dnsforwarder_status" | sed -e 's/[^0-9]*//g')
  fi
  if [ -z "$pid" ]; then
    echo "$(date +'%Y-%m-%d %H:%M:%S') dnsforwarder没有运行,开始拉起进程"
    zxzl
    if [ $? -ne 0 ]; then
      echo "$(date +'%Y-%m-%d %H:%M:%S') 拉起失败"
      break
    fi
  fi
else
  echo"$(date +'%Y-%m-%d %H:%M:%S') 查询指令失败"
  break
fi
sleep 30
done
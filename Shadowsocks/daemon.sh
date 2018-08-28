#!/system/bin/sh

alias now_time="date +'%Y-%m-%d %H:%M:%S'"
now_path="dirn"
pid_file="dirp"
max=10
intervals=30
daemon_list=(
  "dnsforwarder"
)
if [ ! -d $now_path ]; then
  now_path="${0%/*}"
fi
if [[ $(id -u) -eq 0 ]]; then
  echo "$(now_time) 守护脚本开始运行..."
  echo "$(now_time) 运行目录路径: $now_path"
  echo "$(now_time) 运行PID: $$"
  echo "$(now_time) PID写出文件: $pid_file"
  echo "$(now_time) 失败上限: $max 次"
  echo "$(now_time) 检查间隔: $intervals 秒"
  echo $$ > $pid_file
else
  echo "$(now_time) 请使用ROOT权限执行脚本！"
  exit
fi
while true; do
  new_pid=$(cat $pid_file)
  if [[ ! -f $0 || $$ -ne $((new_pid)) ]]; then
    echo "$(now_time) 守护脚本被删除或PID文件值改变"
    echo "$(now_time) 运行PID: $$ 自动退出..."
    break
  fi
  if [[ -f /system/bin/pgrep || -f /system/xbin/pgrep ]]; then
    for i in ${daemon_list[@]}; do
      pid=$(pgrep $i)
      if [ $((pid)) -lt 100 ]; then
        echo "$(now_time) $i 没有运行,开始重启运行脚本..."
        if [[ "$i" == "$last_status" && ${#daemon_list[@]} -gt 1 ]]; then
          echo "$(now_time) 再次重启脚本 $i 也没有运行成功，强制退出！"
          exit
        else        
          last_status=$i
          ${now_path}/stop.sh 2>/dev/null
          ${now_path}/start.sh 2>/dev/null
        fi
        if [ $? -ne 0 ]; then
          echo "$(now_time) 重启脚本失败！"
          ((max--))
          if [ $max -lt 0 ]; then
            echo "$(now_time) 达到失败上限，强制退出！"
            exit
          fi
        else
          echo "$(now_time) 重启脚本完成。"
          break
        fi
      fi
    done  
  else
    echo "$(now_time) 查询指令失败！"
    break
  fi
sleep $intervals
done
The .bashrc solution proposed by Kirby worked for me:

set-title(){
  ORIG=$PS1
  TITLE="\e]2;$@\a"
  PS1=${ORIG}${TITLE}
}
then from my prompt: $ set-title test-title

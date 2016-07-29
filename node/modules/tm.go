package node

import (
    "encoding/json"
    "io/ioutil"
    "os"
)

type ChainArgs struct {
  Index string
  Selector string
}

type Unit struct {
  Index int
  Order int
  Require []string
  Source string
  Name string
  Hookup string

  // optional
  Mode string
  Args map[string]string
  ChainArgs map[string]ChainArgs
}

type UnitBlock struct {
  Units []Unit
  ClientId string
}

type TaskManager struct {
}

func (tm *TaskManager) Parse(message [][]byte) (unitBlock UnitBlock) {
    unitBlock = UnitBlock{}
    err := json.Unmarshal(message[0], &unitBlock)
    Check(err)
    return unitBlock
}

func (tm *TaskManager) SaveToVolume(path string, fileName string, source string) {
  err := os.MkdirAll(path, 0777)
  Check(err)
  err = ioutil.WriteFile(path + "/" + fileName, []byte(source), 0777)
  Check(err)
}

func (tm *TaskManager) SaveToCache(unitBlock UnitBlock) {
}

func (tm *TaskManager) Voting(core NodeCore) (nodes []string) {
  return []string{}
}

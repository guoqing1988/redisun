<?php
namespace Limen\RedModel\Commands;

class ZaddCommand extends Command
{
    public function getScript()
    {
        $elementsPart = $this->joinArguments();
        $luaSetTtl = $this->luaSetTtl($this->getTtl());
        $setTtl = $luaSetTtl ? 1 : 0;
        $checkScript = $this->existenceScript;

        $script = <<<LUA
$checkScript
local values = {};
local setTtl = '$setTtl';
for i,v in ipairs(KEYS) do
    local rs1 = redis.pcall('zadd', v, $elementsPart);
    if rs1 then
        if setTtl=='1' then
            $luaSetTtl
        end
        values[#values+1] = rs1;
    else 
        values[#values+1] = nil;
    end
end 
return {KEYS,values};
LUA;
        return $script;
    }

}
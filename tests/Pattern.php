
    public function testFormatPattern()
    {
        $this->assertEquals('(?<id>\d+)', $this->collection->getPattern()->format('<int:id>'));
        $this->assertEquals('(?<name>\w+)', $this->collection->getPattern()->format('<str:name>'));
    }
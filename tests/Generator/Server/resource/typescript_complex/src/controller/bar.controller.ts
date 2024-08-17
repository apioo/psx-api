import { Controller, Get, Post, Put, Patch, Delete, HttpCode, Param, Query, Headers, Body } from '@nestjs/common'

@Controller()
export class BarController {
  @Get()
  @HttpCode(200)
  find(@Param('foo') foo: string): EntryCollection {
    // @TODO implement method
  }

  @Post()
  @HttpCode(201)
  put(@Body() payload: EntryCreate): EntryMessage {
    // @TODO implement method
  }

}

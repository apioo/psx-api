import { Controller, Get, Post, Put, Patch, Delete, HttpCode, Param, Query, Headers, Body } from '@nestjs/common'

@Controller()
export class BazController {
  @Get()
  @HttpCode(200)
  get(@Param('year') year: string): EntryCollection {
    // @TODO implement method
  }

  @Post()
  @HttpCode(201)
  create(@Body() payload: EntryCreate): EntryMessage {
    // @TODO implement method
  }

}

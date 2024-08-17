import { Controller, Get, Post, Put, Patch, Delete, HttpCode, Param, Query, Headers, Body } from '@nestjs/common'

@Controller()
export class BarController {
  @Get()
  @HttpCode(200)
  get(): EntryCollection {
    // @TODO implement method
  }

  @Post()
  @HttpCode(201)
  create(@Body() payload: EntryCreate): EntryMessage {
    // @TODO implement method
  }

}

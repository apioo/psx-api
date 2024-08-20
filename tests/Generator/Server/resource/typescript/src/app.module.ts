import { Module } from '@nestjs/common';
import { AppController } from './controller/app.controller';

@Module({
  imports: [],
  controllers: [AppController],
  providers: [],
})
export class AppModule {}

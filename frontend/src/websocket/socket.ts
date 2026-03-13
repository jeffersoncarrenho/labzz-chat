import { SocketEvent } from "@/types/socket";

export class ChatSocket {
  private socket: WebSocket;

  constructor(token: string) {
    this.socket = new WebSocket(`ws://localhost:8081?token=${token}`);
  }

  onMessage(callback: (data: SocketEvent) => void) {
    this.socket.onmessage = (event: MessageEvent<string>) => {
      const data: SocketEvent = JSON.parse(event.data);

      callback(data);
    };
  }

  send(data: SocketEvent) {
    this.socket.send(JSON.stringify(data));
  }
}

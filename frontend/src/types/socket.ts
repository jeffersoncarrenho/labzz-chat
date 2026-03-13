export type SocketEventType = "message" | "typing" | "connected" | "error";

export interface SocketEventBase {
  type: SocketEventType;
}

export interface MessageEvent extends SocketEventBase {
  type: "message";
  conversation_id: number;
  sender_id: number;
  content: string;
  created_at?: string;
}

export interface TypingEvent extends SocketEventBase {
  type: "typing";
  conversation_id: number;
  user_id: number;
}

export type SocketEvent = MessageEvent | TypingEvent;

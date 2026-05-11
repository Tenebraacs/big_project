using System;
using System.Text.Json;
using System.Text.Json.Serialization;

namespace SideQuestJar.Serialization
{
    /// <summary>
    /// Laravel serializes boolean columns as JSON true/false; the app stores flags as int (0/1).
    /// </summary>
    public sealed class BoolOrIntJsonConverter : JsonConverter<int>
    {
        public override int Read(ref Utf8JsonReader reader, Type typeToConvert, JsonSerializerOptions options)
        {
            return reader.TokenType switch
            {
                JsonTokenType.True => 1,
                JsonTokenType.False => 0,
                JsonTokenType.Number => reader.TryGetInt32(out var i) ? i : (reader.GetDouble() != 0 ? 1 : 0),
                JsonTokenType.String => int.TryParse(reader.GetString(), out var s) ? s : 0,
                JsonTokenType.Null => 0,
                _ => throw new JsonException($"Cannot convert {reader.TokenType} to int for boolean-like field.")
            };
        }

        public override void Write(Utf8JsonWriter writer, int value, JsonSerializerOptions options)
        {
            writer.WriteNumberValue(value);
        }
    }
}
